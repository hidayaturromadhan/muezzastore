<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\DigiflazzService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckDigiflazzProcessingOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1; // ini job "polling", jangan retry otomatis biar gak dobel spam

    public function __construct(
        public int $limit = 50
    ) {}

    public function handle(DigiflazzService $digiflazz): void
    {
        $limit = $this->limit > 0 ? $this->limit : 50;

        $orders = Order::with('product')
            ->where('status', 'processing')
            ->whereNotNull('digiflazz_trx_id')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        if ($orders->isEmpty()) {
            Log::info('DIGIFLAZZ_CHECK_NO_ORDERS');
            return;
        }

        foreach ($orders as $order) {
            try {
                // safety: kalau product kosong skip
                if (!$order->product) continue;

                // safety: kalau status sudah final skip (harusnya gak keambil sih, tapi guard)
                if (in_array($order->status, ['fulfilled', 'failed'], true)) continue;

                $sku        = (string) ($order->product->buyer_sku_code ?? '');
                $customerNo = trim((string) ($order->target ?? ''));
                $refId      = (string) ($order->digiflazz_trx_id ?? '');

                if ($sku === '' || $customerNo === '' || $refId === '') continue;

                // Tentukan postpaid/prepaid (default prepaid)
                // Kalau kamu punya flag di product, gunakan itu.
                // Jika tidak ada, hasilnya false.
                $isPostpaid = false;
                if (isset($order->product->is_postpaid)) {
                    $isPostpaid = (bool) $order->product->is_postpaid;
                } elseif (isset($order->product->type)) {
                    // opsional: kalau ada kolom type (mis: 'postpaid'/'prepaid')
                    $isPostpaid = strtolower((string) $order->product->type) === 'postpaid';
                }

                // Lock per-order supaya aman kalau 2 worker cek bareng
                $locked = DB::transaction(function () use ($order) {
                    return Order::where('id', $order->id)
                        ->lockForUpdate()
                        ->first();
                });

                if (!$locked) continue;

                // double-check setelah lock
                if ($locked->status !== 'processing') continue;

                // IMPORTANT: jangan pakai named argument yang rawan typo.
                // Panggil positional aja.
                $resp = $digiflazz->checkTransaction(
                    $refId,
                    $sku,
                    $customerNo,
                    $isPostpaid
                );

                $this->apply($locked, $resp);

            } catch (\Throwable $e) {
                Log::error('DIGIFLAZZ_CHECK_ERROR', [
                    'order_id' => $order->id,
                    'ref_id'   => $order->digiflazz_trx_id,
                    'err'      => $e->getMessage(),
                ]);
            }
        }
    }

    private function apply(Order $order, array $resp): void
    {
        $dfStatus  = (string) data_get($resp, 'data.status', '');
        $dfRc      = (string) data_get($resp, 'data.rc', '');
        $dfMessage = (string) data_get($resp, 'data.message', '');
        $dfSn      = (string) data_get($resp, 'data.sn', '');

        DB::transaction(function () use ($order, $resp, $dfStatus, $dfRc, $dfMessage, $dfSn) {
            // simpan raw response (wajib untuk audit/debug)
            $order->digiflazz_response = $resp;

            $order->digiflazz_status  = $dfStatus !== '' ? $dfStatus : null;
            $order->digiflazz_rc      = $dfRc !== '' ? $dfRc : null;
            $order->digiflazz_message = $dfMessage !== '' ? $dfMessage : null;

            // sn kadang null; normalisasi
            if ($dfSn !== '') {
                $order->digiflazz_sn = $dfSn;

                // optional token kolom ada
                if (isset($order->digiflazz_token)) {
                    $tokenOnly = trim(explode('/', $dfSn)[0] ?? '');
                    if ($tokenOnly !== '') $order->digiflazz_token = $tokenOnly;
                }
            }

            // status mapping standar
            if (strcasecmp($dfStatus, 'Sukses') === 0) {
                $order->status = 'fulfilled';
            } elseif (strcasecmp($dfStatus, 'Gagal') === 0) {
                $order->status = 'failed';
            } else {
                // Pending / empty / unknown => tetap processing
                $order->status = 'processing';
            }

            $order->save();
        });

        Log::info('DIGIFLAZZ_CHECK_UPDATED', [
            'order_id'        => $order->id,
            'ref_id'          => $order->digiflazz_trx_id,
            'status'          => $order->status,
            'digiflazz_status'=> $order->digiflazz_status,
            'digiflazz_rc'    => $order->digiflazz_rc,
        ]);
    }
}
