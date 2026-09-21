<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (request()->header('x-forwarded-proto') === 'https' || request()->isSecure()) {
            URL::forceScheme('https');
        }

        Paginator::useBootstrapFive();
    }
}
