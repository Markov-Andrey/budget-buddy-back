<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process discord and execute queue';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('queue:currency');
        $this->call('queue:crypto');
        $this->call('queue:work');

        $this->info('All commands have been executed.');

        return 0;
    }
}
