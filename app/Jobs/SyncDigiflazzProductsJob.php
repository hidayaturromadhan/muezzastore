<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\DigiflazzService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncDigiflazzProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(DigiflazzService $digiflazz): void
    {
        $items = $digiflazz->priceList();

        Log::info('DIGIFLAZZ_SYNC_START', ['count' => count($items)]);

        DB::transaction(function () use ($items) {
            foreach ($items as $it) {
                // map field umum Digiflazz (sesuaikan jika berbeda)
                $sku = (string) ($it['buyer_sku_code'] ?? $it['sku'] ?? '');
                if ($sku === '') continue;

                $name = (string) ($it['product_name'] ?? $it['product'] ?? $it['name'] ?? $sku);
                $category = (string) ($it['category'] ?? 'Unknown');
                $brand = (string) ($it['brand'] ?? 'Unknown');
                $type = (string) ($it['type'] ?? null);
                $price = (int) ($it['price'] ?? 0);
                $status = (string) ($it['status'] ?? null);
                $seller = (string) ($it['seller_name'] ?? null);

                // target_type: simple mapping
                // Pulsa/Data biasanya nomor hp
                // PLN biasanya customer
                $targetType = 'phone';
                if (stripos($category, 'pln') !== false) {
                    $targetType = 'customer';
                }

                // harga jual: default = modal + margin (contoh 1000)
                // nanti kamu bisa buat tabel margin/setting
                $sellPrice = $price > 0 ? ($price + 1000) : 0;

                Product::updateOrCreate(
                    ['buyer_sku_code' => $sku],
                    [
                        'product_name' => $name,
                        'category' => $category,
                        'brand' => $brand,
                        'type' => $type,
                        'digiflazz_price' => $price,
                        'price' => $sellPrice,
                        'digiflazz_status' => $status,
                        'seller_name' => $seller,
                        'target_type' => $targetType,
                        'is_active' => true,
                    ]
                );
            }
        });

        Log::info('DIGIFLAZZ_SYNC_DONE');
    }
}
