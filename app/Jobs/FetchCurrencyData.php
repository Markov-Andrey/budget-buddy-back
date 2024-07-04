<?php

namespace App\Jobs;

use App\Models\InvestmentType;
use App\Services\CryptoService;
use App\Services\CurrencyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchCurrencyData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $currency;

    public function __construct($currency)
    {
        $this->currency = $currency;
    }

    public function handle(): void
    {
        try {
            CurrencyService::getCurrencyData($this->currency);
        } catch (\Exception $e) {
            Log::error("Error fetching data for currency {$this->currency}: " . $e->getMessage());
        }
    }
}
