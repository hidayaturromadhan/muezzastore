<?php

namespace App\Http\Controllers;

use App\Jobs\FulfillOrderJob;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('MIDTRANS_WEBHOOK_HIT', $payload);

        $orderId      = (string) ($payload['order_id'] ?? '');
        $statusCode   = (string) ($payload['status_code'] ?? '');
        $grossAmount  = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === '') {
            Log::warning('MIDTRANS_WEBHOOK_INVALID_PAYLOAD', $payload);
            return response()->json(['message' => 'ok'], 200);
        }

        if (!MidtransService::verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('MIDTRANS_WEBHOOK_INVALID_SIGNATURE', [
                'order_id' => $orderId,
                'status_code' => $statusCode,
                'gross_amount' => $grossAmount,
                'signature_key' => $signatureKey,
            ]);
            return response()->json(['message' => 'invalid signature'], 401);
        }

        $order = Order::where('midtrans_order_id', $orderId)->first();

        // Midtrans "Test notification URL" sering pakai order_id dummy -> tetap 200
        if (!$order) {
            Log::warning('MIDTRANS_WEBHOOK_ORDER_NOT_FOUND', ['order_id' => $orderId]);
            return response()->json(['message' => 'ok'], 200);
        }

        // Simpan payload terakhir untuk audit
        $order->midtrans_payload = $payload;

        $trxStatus = (string) ($payload['transaction_status'] ?? '');
        $fraud     = (string) ($payload['fraud_status'] ?? '');

        // Jangan downgrade status kalau sudah fulfilled
        $current = (string) $order->status;

        // Mapping Midtrans -> status internal
        $newStatus = $current;

        if ($trxStatus === 'capture') {
            $newStatus = ($fraud === 'challenge') ? 'pending_payment' : 'paid';
        } elseif ($trxStatus === 'settlement') {
            $newStatus = 'paid';
        } elseif ($trxStatus === 'pending') {
            $newStatus = 'pending_payment';
        } elseif (in_array($trxStatus, ['deny', 'cancel', 'expire'], true)) {
            $newStatus = 'failed';
        } else {
            Log::info('MIDTRANS_WEBHOOK_UNHANDLED_STATUS', [
                'order_id' => $orderId,
                'transaction_status' => $trxStatus,
            ]);
        }

        // Guard: kalau sudah fulfilled, jangan ditimpa jadi pending/paid lagi
        if ($current === 'fulfilled') {
            $order->save();
            return response()->json(['message' => 'ok'], 200);
        }

        $order->status = $newStatus;
        $order->save();

        // Dispatch fulfill hanya saat paid (idempotent dijaga di Job)
        if ($order->status === 'paid') {
            FulfillOrderJob::dispatch($order->id);
        }

        return response()->json(['message' => 'ok'], 200);
    }
}
