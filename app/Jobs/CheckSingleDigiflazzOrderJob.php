<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\DigiflazzService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckSingleDigiflazzOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $orderId) {}

    public function handle(DigiflazzService $digiflazz): void
    {
        $order = Order::with('product')->find($this->orderId);
        if (!$order || !$order->product) return;

        $refId = (string) ($order->digiflazz_trx_id ?? '');
        if ($refId === '') return;

        $sku = (string) ($order->product->buyer_sku_code ?? '');
        $customerNo = trim((string) $order->target);

        if ($sku === '' || $customerNo === '') return;

        try {
            $resp = $digiflazz->checkTransaction(
                refId: $refId,
                buyerSkuCode: $sku,
                customerNo: $customerNo,
                isPostpaid: false
            );

            $dfStatus = (string) data_get($resp, 'data.status', '');
            $rc       = (string) data_get($resp, 'data.rc', '');
            $msg      = (string) data_get($resp, 'data.message', '');
            $sn       = (string) data_get($resp, 'data.sn', '');

            $order->digiflazz_response = $resp;
            $order->digiflazz_status  = $dfStatus !== '' ? $dfStatus : null;
            $order->digiflazz_rc      = $rc !== '' ? $rc : null;
            $order->digiflazz_message = $msg !== '' ? $msg : null;

            if ($sn !== '') {
                $order->digiflazz_sn = $sn;
            }

            if (strcasecmp($dfStatus, 'Sukses') === 0) {
                $order->status = 'fulfilled';
            } elseif (strcasecmp($dfStatus, 'Gagal') === 0) {
                $order->status = 'failed';
            } else {
                $order->status = 'processing';
            }

            $order->save();

            Log::info('DIGIFLAZZ_CHECK_SINGLE_UPDATED', [
                'order_id' => $order->id,
                'ref_id' => $refId,
                'status' => $order->status,
                'df_status' => $dfStatus,
                'rc' => $rc,
            ]);
        } catch (\Throwable $e) {
            Log::error('DIGIFLAZZ_CHECK_SINGLE_ERROR', [
                'order_id' => $order->id,
                'ref_id' => $refId,
                'err' => $e->getMessage(),
            ]);
        }
    }
}
