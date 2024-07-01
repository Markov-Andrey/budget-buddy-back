<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'go';

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
        $this->call('queue:crypto');
        $this->call('queue:discord');
        $this->call('queue:work');

        $this->info('All commands have been executed.');

        return 0;
    }
}
