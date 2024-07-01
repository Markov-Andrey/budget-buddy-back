<?php

namespace App\Jobs;

use App\Models\InvestmentType;
use App\Services\CryptoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchCryptoData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $cryptoList = InvestmentType::query()->pluck('coingecko_id')->toArray();

        foreach ($cryptoList as $crypto) {
            try {
                CryptoService::getCryptoData($crypto);
            } catch (\Exception $e) {
                Log::error("Error fetching data for crypto {$crypto}: " . $e->getMessage());
            }

            sleep(2); // Ожидание 2 секунды между запросами
        }
    }
}
