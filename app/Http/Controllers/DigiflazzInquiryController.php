<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DigiflazzInquiryController extends Controller
{
    public function pln(Request $request, Product $product, DigiflazzService $digiflazz)
    {
        abort_unless((bool) $product->is_active, 404);
        abort_unless($product->target_type === 'pln', 404);

        $data = $request->validate([
            'customer_no' => ['required', 'string', 'min:6', 'max:30', 'regex:/^[0-9]+$/'],
        ], [
            'customer_no.regex' => 'ID PLN hanya boleh angka.',
        ]);

        $customerNo = trim($data['customer_no']);

        try {
            $resp = $digiflazz->inquiryPln($customerNo);

            $payload = $resp['data'] ?? null;
            if (!is_array($payload)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Response Digiflazz tidak valid.',
                    'raw' => $resp,
                ], 422);
            }

            $rc = (string)($payload['rc'] ?? '');
            $status = (string)($payload['status'] ?? '');
            $message = (string)($payload['message'] ?? 'Unknown');

            // Sukses sesuai docs: rc=00 dan status=Sukses
            if ($rc !== '00' || mb_strtolower($status) !== 'sukses') {
                return response()->json([
                    'ok' => false,
                    'message' => $message ?: 'Gagal cek PLN.',
                    'data' => $payload,
                ], 422);
            }

            // Simpan hasil inquiry ke session untuk dipakai preview
            $key = $this->sessionKey($product->id, $customerNo);

            $saved = [
                'customer_no' => (string)($payload['customer_no'] ?? $customerNo),
                'name' => $payload['name'] ?? null,
                'segment_power' => $payload['segment_power'] ?? null,
                'subscriber_id' => $payload['subscriber_id'] ?? null,
                'meter_no' => $payload['meter_no'] ?? null,
                'rc' => $rc,
                'status' => $status,
                'message' => $message,
                'saved_at' => now()->toDateTimeString(),
            ];

            $request->session()->put($key, $saved);

            return response()->json([
                'ok' => true,
                'message' => 'Inquiry PLN sukses.',
                'data' => $saved,
            ]);
        } catch (\Throwable $e) {
            Log::error('PLN_INQUIRY_ERROR', [
                'product_id' => $product->id,
                'customer_no' => $customerNo,
                'err' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Gagal cek PLN. Coba lagi.',
            ], 500);
        }
    }

    private function sessionKey(int $productId, string $customerNo): string
    {
        return "pln_inquiry.{$productId}." . trim($customerNo);
    }
}
