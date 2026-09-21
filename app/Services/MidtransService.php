<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public static function bootConfig(): void
    {
        $cfg = config('services.midtrans');

        Config::$serverKey = $cfg['server_key'];
        Config::$isProduction = $cfg['is_production'];
        Config::$isSanitized = $cfg['sanitized'];
        Config::$is3ds = $cfg['is_3ds'];
    }

    public function createSnapRedirect(array $params): array
    {
        self::bootConfig();

        // Snap::createTransaction akan return token dan redirect_url (Snap Redirect)
        // :contentReference[oaicite:2]{index=2}
        $trx = Snap::createTransaction($params);

        return [
            'token' => $trx->token ?? null,
            'redirect_url' => $trx->redirect_url ?? null,
            'raw' => (array) $trx,
        ];
    }

    public static function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        // signature_key = sha512(order_id + status_code + gross_amount + server_key)
        // :contentReference[oaicite:3]{index=3}
        $serverKey = config('services.midtrans.server_key');
        $calculated = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        return hash_equals($calculated, $signatureKey);
    }
}
