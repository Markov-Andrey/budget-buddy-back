<?php

namespace App\Console\Commands;

use App\Jobs\FetchCurrencyData;
use App\Models\InvestmentType;
use Illuminate\Console\Command;

class GetCurrencyPrice extends Command
{
    protected $signature = 'queue:currency';
    protected $description = 'Update currency rates';

    public function handle(): int
    {
        $cryptoList = InvestmentType::query()
            ->whereNotNull('nbrb_id')
            ->where('nbrb_id', '!=', '')
            ->pluck('nbrb_id')
            ->toArray();

        foreach ($cryptoList as $crypto) {
            FetchCurrencyData::dispatch($crypto)->onQueue('default');
        }

        $this->info('Currency prices update dispatched to queue.');

        return 0;
    }
}
