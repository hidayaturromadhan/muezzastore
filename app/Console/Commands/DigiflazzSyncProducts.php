<?php

namespace App\Console\Commands;

use App\Jobs\SyncDigiflazzProductsJob;
use Illuminate\Console\Command;

class DigiflazzSyncProducts extends Command
{
    protected $signature = 'digiflazz:sync-products';
    protected $description = 'Sync product price list from Digiflazz into products table';

    public function handle(): int
    {
        SyncDigiflazzProductsJob::dispatch();
        $this->info('SyncDigiflazzProductsJob dispatched.');
        return self::SUCCESS;
    }
}
