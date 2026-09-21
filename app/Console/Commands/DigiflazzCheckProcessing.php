<?php

namespace App\Console\Commands;

use App\Jobs\CheckDigiflazzProcessingOrdersJob;
use Illuminate\Console\Command;

class DigiflazzCheckProcessing extends Command
{
    /**
     * Jalankan cek order processing Digiflazz.
     * --sync  : jalankan langsung (tanpa queue) untuk testing cepat
     * --limit : batasi jumlah order yang dicek per run
     */
    protected $signature = 'digiflazz:check-processing {--sync : Run immediately without queue} {--limit=50 : Max orders to check per run}';

    protected $description = 'Check Digiflazz transactions for orders with processing status';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        if ($limit <= 0) $limit = 50;

        if ($this->option('sync')) {
            // jalan langsung tanpa queue (enak buat testing)
            (new CheckDigiflazzProcessingOrdersJob($limit))->handle(app(\App\Services\DigiflazzService::class));
            $this->info("Checked processing orders (sync). limit={$limit}");
            return self::SUCCESS;
        }

        // default: dispatch ke queue
        CheckDigiflazzProcessingOrdersJob::dispatch($limit);
        $this->info("CheckDigiflazzProcessingOrdersJob dispatched. limit={$limit}");

        return self::SUCCESS;
    }
}
