<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DigiflazzWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $secret = config('services.digiflazz.webhook_secret');
        $signature = $request->header('X-Hub-Signature');

        if ($signature) {
            $payloadRaw = $request->getContent();
            $hash = 'sha1=' . hash_hmac('sha1', $payloadRaw, $secret);

            if (!hash_equals($hash, $signature)) {
                Log::warning('DIGIFLAZZ_WEBHOOK_INVALID_SIGNATURE', [
                    'ip' => $request->ip(),
                    'provided_signature' => $signature,
                ]);
                return response()->json(['message' => 'Invalid signature'], 401);
            }
        } else {
            Log::warning('DIGIFLAZZ_WEBHOOK_MISSING_SIGNATURE', ['ip' => $request->ip()]);
            return response()->json(['message' => 'Missing signature'], 401);
        }

        $payload = $request->all();
        $data = $payload['data'] ?? $payload;

        Log::info('DIGIFLAZZ_WEBHOOK_HIT', $payload);

        $refId  = (string) ($data['ref_id'] ?? '');
        $status = (string) ($data['status'] ?? '');

        if ($refId === '' || $status === '') {
            Log::warning('DIGIFLAZZ_WEBHOOK_INVALID_PAYLOAD', $payload);
            return response()->json(['message' => 'ok'], 200);
        }

        $order = Order::where('digiflazz_trx_id', $refId)->first();
        if (!$order) {
            Log::warning('DIGIFLAZZ_WEBHOOK_ORDER_NOT_FOUND', ['ref_id' => $refId]);
            return response()->json(['message' => 'ok'], 200);
        }

        $this->applyDigiflazzData($order, $data);

        return response()->json(['message' => 'ok'], 200);
    }

    private function applyDigiflazzData(Order $order, array $data): void
    {
        $dfStatus  = (string) ($data['status'] ?? '');
        $dfRc      = (string) ($data['rc'] ?? '');
        $dfMessage = (string) ($data['message'] ?? '');
        $dfSn      = (string) ($data['sn'] ?? '');

        DB::transaction(function () use ($order, $data, $dfStatus, $dfRc, $dfMessage, $dfSn) {
            $order->digiflazz_response = $data;
            $order->digiflazz_status  = $dfStatus !== '' ? $dfStatus : null;
            $order->digiflazz_rc      = $dfRc !== '' ? $dfRc : null;
            $order->digiflazz_message = $dfMessage !== '' ? $dfMessage : null;

            if ($dfSn !== '') {
                $order->digiflazz_sn = $dfSn;

                if (isset($order->digiflazz_token)) {
                    $tokenOnly = trim(explode('/', $dfSn)[0] ?? '');
                    if ($tokenOnly !== '') $order->digiflazz_token = $tokenOnly;
                }
            }

            $statusLower = strtolower($dfStatus);
            if ($statusLower === 'sukses') {
                $order->status = 'fulfilled';
            } elseif ($statusLower === 'gagal') {
                $order->status = 'failed';
            } else {
                $order->status = 'processing';
            }

            $order->save();
        });

        Log::info('DIGIFLAZZ_WEBHOOK_UPDATED', [
            'order_id' => $order->id,
            'ref_id' => $order->digiflazz_trx_id,
            'status' => $order->status,
            'digiflazz_status' => $order->digiflazz_status,
            'digiflazz_rc' => $order->digiflazz_rc,
        ]);
    }
}
