<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigiflazzService
{
    private string $username;
    private string $apiKey;
    private string $baseUrl;
    private ?string $cbUrl;

    public function __construct()
    {
        $cfg = config('services.digiflazz');

        $this->username = (string) ($cfg['username'] ?? '');
        $this->apiKey   = (string) ($cfg['api_key'] ?? '');

        // Support 2 gaya base_url:
        // - https://api.digiflazz.com/v1
        // - https://api.digiflazz.com  (akan jadi /v1)
        $rawBase = (string) ($cfg['base_url'] ?? 'https://api.digiflazz.com');
        $rawBase = rtrim($rawBase, '/');

        if (str_ends_with($rawBase, '/v1')) {
            $this->baseUrl = $rawBase;
        } else {
            $this->baseUrl = $rawBase . '/v1';
        }

        // Prefer config (biar aman kalau config:cache), fallback env
        $cb = (string) ($cfg['cb_url'] ?? env('DIGIFLAZZ_WEBHOOK_URL') ?? '');
        $cb = trim($cb);
        $this->cbUrl = $cb !== '' ? $cb : null;

        if ($this->username === '' || $this->apiKey === '') {
            throw new \RuntimeException('Digiflazz config missing. Set DIGIFLAZZ_USERNAME and DIGIFLAZZ_API_KEY');
        }
    }

    private function sign(string $value): string
    {
        return md5($this->username . $this->apiKey . $value);
    }

    /**
     * NOTE:
     * - Parameter $path wajib PATH (contoh: '/cek-saldo'), BUKAN URL FULL.
     */
    private function postJson(string $path, array $payload, string $logKey): array
    {
        $url = $this->baseUrl . '/' . ltrim($path, '/');

        $resp = Http::retry(
                3,
                500,
                function ($exception) {
                    if ($exception instanceof ConnectionException) return true;

                    $response = method_exists($exception, 'response') ? $exception->response : null;
                    if ($response) {
                        $status = $response->status();
                        return $status >= 500 || $status === 429;
                    }
                    return false;
                }
            )
            ->connectTimeout(10)
            ->timeout(30)
            ->acceptJson()
            ->asJson()
            ->post($url, $payload);

        $body = $resp->json();

        Log::info($logKey, [
            'url'         => $url,
            'payload'     => $payload,
            'http_status' => $resp->status(),
            'body'        => $body ?? $resp->body(),
        ]);

        if (!$resp->successful()) {
            $msg = is_array($body) ? json_encode($body) : (string) $resp->body();
            throw new \RuntimeException("Digiflazz HTTP error: {$resp->status()} | {$msg}");
        }

        return is_array($body) ? $body : [];
    }

    public function priceList(): array
    {
        $cmd = 'pricelist';

        $payload = [
            'cmd'      => $cmd,
            'username' => $this->username,
            'sign'     => $this->sign($cmd),
        ];

        $json = $this->postJson('/price-list', $payload, 'DIGIFLAZZ_PRICELIST');

        $data = $json['data'] ?? null;
        if (!is_array($data)) {
            throw new \RuntimeException('Digiflazz priceList invalid response structure');
        }

        return $data;
    }

    /**
     * PREPAID Transaction
     * check status prepaid = topup ulang dengan ref_id yang sama.
     */
    public function createTransaction(
        string $refId,
        string $buyerSkuCode,
        string $customerNo,
        bool $testing = false,
        ?int $maxPrice = null
    ): array {
        $payload = [
            'username'       => $this->username,
            'buyer_sku_code' => $buyerSkuCode,
            'customer_no'    => $customerNo,
            'ref_id'         => $refId,
            'sign'           => $this->sign($refId),
        ];

        if ($testing) $payload['testing'] = true;
        if ($maxPrice !== null) $payload['max_price'] = (int) $maxPrice;
        if ($this->cbUrl) $payload['cb_url'] = $this->cbUrl;

        return $this->postJson('/transaction', $payload, 'DIGIFLAZZ_TX_REQUEST');
    }

    public function checkStatusPrepaid(string $refId, string $buyerSkuCode, string $customerNo): array
    {
        return $this->createTransaction($refId, $buyerSkuCode, $customerNo, false);
    }

    public function checkStatusPostpaid(string $refId, string $buyerSkuCode, string $customerNo): array
    {
        $payload = [
            'commands'       => 'status-pasca',
            'username'       => $this->username,
            'buyer_sku_code' => $buyerSkuCode,
            'customer_no'    => $customerNo,
            'ref_id'         => $refId,
            'sign'           => $this->sign($refId),
        ];

        return $this->postJson('/transaction', $payload, 'DIGIFLAZZ_STATUS_PASCA');
    }

    /**
     * BACKWARD COMPATIBLE:
     * bisa dipanggil pakai named argument:
     * - isPostpaid: true/false (baru)
     * - is_postpaid: true/false (lama)
     */
    public function checkTransaction(
        string $refId,
        string $buyerSkuCode,
        string $customerNo,
        bool $isPostpaid = false,
        ?bool $is_postpaid = null
    ): array {
        if ($is_postpaid !== null) {
            $isPostpaid = (bool) $is_postpaid;
        }

        return $isPostpaid
            ? $this->checkStatusPostpaid($refId, $buyerSkuCode, $customerNo)
            : $this->checkStatusPrepaid($refId, $buyerSkuCode, $customerNo);
    }

    public function inquiryPln(string $customerNo): array
    {
        $customerNo = trim($customerNo);

        $payload = [
            'username'    => $this->username,
            'customer_no' => $customerNo,
            'sign'        => $this->sign($customerNo),
        ];

        return $this->postJson('/inquiry-pln', $payload, 'DIGIFLAZZ_INQUIRY_PLN');
    }

    /**
     * Digiflazz cek saldo:
     * POST /cek-saldo
     * cmd=deposit
     * sign = md5(username + apiKey + "depo")
     */
    public function checkBalance(): array
    {
        $payload = [
            'cmd'      => 'deposit',
            'username' => $this->username,
            'sign'     => md5($this->username . $this->apiKey . 'depo'),
        ];

        return $this->postJson('/cek-saldo', $payload, 'DIGIFLAZZ_BALANCE');
    }

    /**
     * Ambil angka saldo dari payload Digiflazz (deposit/saldo).
     */
    public function getBalanceValueFromPayload(array $payload): ?int
    {
        $balance = data_get($payload, 'data.deposit');
        if ($balance === null) $balance = data_get($payload, 'data.saldo');

        return is_numeric($balance) ? (int) $balance : null;
    }

    /**
     * Saldo cached (biar tidak spam API).
     * Return: int saldo atau null jika format tidak valid.
     */
    public function getBalanceValueCached(int $ttlSeconds = 30): ?int
    {
        $payload = Cache::remember('digiflazz.balance.payload', $ttlSeconds, function () {
            return $this->checkBalance();
        });

        return is_array($payload) ? $this->getBalanceValueFromPayload($payload) : null;
    }
}
