<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\DigiflazzService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FulfillOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $backoff = 30;

    public function __construct(
        public int $orderId,
        public bool $force = false, // kalau admin retry -> true
    ) {}

    public function handle(DigiflazzService $digiflazz): void
    {
        // lock row biar 2 job gak tembak dobel
        $order = DB::transaction(function () {
            return Order::with('product')
                ->where('id', $this->orderId)
                ->lockForUpdate()
                ->first();
        });

        if (!$order) {
            throw new ModelNotFoundException("Order {$this->orderId} not found");
        }

        if (!$order->product) return;

        // normal flow hanya dari webhook midtrans => status harus paid
        // admin retry boleh status failed/processing, tapi force=true
        if (!$this->force && $order->status !== 'paid') return;

        if ($this->force && !in_array($order->status, ['paid','processing','failed'], true)) return;

        // idempotency internal:
        // - kalau sudah fulfilled, stop
        if ($order->status === 'fulfilled') return;

        // kalau masih ada digiflazz_trx_id dan bukan force, jangan tembak ulang
        if (!$this->force && !empty($order->digiflazz_trx_id)) return;

        $sku = (string) ($order->product->buyer_sku_code ?? '');
        if ($sku === '') return;

        $customerNo = trim((string) $order->target);
        if ($customerNo === '') return;

        // buat REF ID baru setiap retry (maksimal pendek)
        $refId = $this->makeRefId($order);

        try {
            // set state dulu: processing + ref id (biar kalau retry worker kebaca)
            DB::transaction(function () use ($order, $refId) {
                $order->status = 'processing';
                $order->digiflazz_trx_id = $refId;
                $order->save();
            });

            $resp = $digiflazz->createTransaction(
                refId: $refId,
                buyerSkuCode: $sku,
                customerNo: $customerNo
            );

            $this->applyDigiflazzResponse($order, $resp);

            Log::info('FULFILL_DONE', ['order_id' => $order->id, 'ref_id' => $refId]);

        } catch (\Throwable $e) {
            Log::error('FULFILL_ERROR', [
                'order_id' => $order->id,
                'ref_id' => $refId,
                'err' => $e->getMessage(),
            ]);

            // biar queue retry
            throw $e;
        }
    }

    private function makeRefId(Order $order): string
    {
        // contoh: DF22-124803NJOCTP-R3A1
        // - base dari midtrans id biar traceable
        $tail = substr(preg_replace('/[^A-Za-z0-9]/', '', (string)$order->midtrans_order_id), -12);

        // random 2 chars biar unik saat retry cepat
        $rand = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));

        $ref = "DF{$order->id}-{$tail}-{$rand}";

        // amankan panjang (Digiflazz biasanya aman <= 30-40; kamu pakai 30 dulu)
        return substr($ref, 0, 30);
    }

    private function applyDigiflazzResponse(Order $order, array $resp): void
    {
        $dfStatus  = (string) data_get($resp, 'data.status', '');
        $dfRc      = (string) data_get($resp, 'data.rc', '');
        $dfMessage = (string) data_get($resp, 'data.message', '');
        $dfSn      = (string) data_get($resp, 'data.sn', '');

        DB::transaction(function () use ($order, $resp, $dfStatus, $dfRc, $dfMessage, $dfSn) {
            $order->digiflazz_response = $resp;

            $order->digiflazz_status  = $dfStatus !== '' ? $dfStatus : null;
            $order->digiflazz_rc      = $dfRc !== '' ? $dfRc : null;
            $order->digiflazz_message = $dfMessage !== '' ? $dfMessage : null;

            if ($dfSn !== '') {
                $order->digiflazz_sn = $dfSn;

                // optional token kolom ada
                if (isset($order->digiflazz_token)) {
                    $tokenOnly = trim(explode('/', $dfSn)[0] ?? '');
                    if ($tokenOnly !== '') $order->digiflazz_token = $tokenOnly;
                }
            }

            if (strcasecmp($dfStatus, 'Sukses') === 0) {
                $order->status = 'fulfilled';
            } elseif (strcasecmp($dfStatus, 'Pending') === 0) {
                $order->status = 'processing';
            } else {
                // termasuk "Gagal"
                $order->status = 'failed';
            }

            $order->save();
        });
    }
}
