<?php

namespace App\Console\Commands;

use App\Jobs\FetchCryptoData;
use App\Models\InvestmentType;
use Illuminate\Console\Command;

class GetCryptoPrice extends Command
{
    protected $signature = 'queue:crypto';
    protected $description = 'Update cryptocurrency rates';

    public function handle(): int
    {
        $cryptoList = InvestmentType::query()
            ->whereNotNull('coingecko_id')
            ->where('coingecko_id', '!=', '')
            ->pluck('coingecko_id')
            ->toArray();

        foreach ($cryptoList as $crypto) {
            FetchCryptoData::dispatch($crypto)->onQueue('default');
        }

        $this->info('Crypto prices update dispatched to queue.');

        return 0;
    }
}
