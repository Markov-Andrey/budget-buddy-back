<?php

namespace App\Console\Commands;

use App\Jobs\FetchCryptoData;
use Illuminate\Console\Command;

class GetCryptoPrice extends Command
{
    protected $signature = 'queue:crypto';
    protected $description = 'Update cryptocurrency rates';

    public function handle(): int
    {
        FetchCryptoData::dispatch()->onQueue('default');
        $this->info('Crypto prices update dispatched to queue.');

        return 0;
    }
}
