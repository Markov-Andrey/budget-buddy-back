<?php

namespace App\Providers;

use App\Models\Receipts;
use App\Models\ReceiptsData;
use App\Observers\ReceiptDataObserver;
use App\Observers\ReceiptObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Receipts::observe(ReceiptObserver::class);
        ReceiptsData::observe(ReceiptDataObserver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
