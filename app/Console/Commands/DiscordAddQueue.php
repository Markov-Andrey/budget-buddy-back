<?php

namespace App\Console\Commands;

use App\Jobs\DiscordProcessJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DiscordAddQueue extends Command
{
    protected $signature = 'queue:discord';
    protected $description = 'Добавление команд из Discord в очередь';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DiscordProcessJob::dispatch();

        $this->info('Discord jobs added to the queue successfully.');
        Log::channel('discord')->info('Discord jobs added to the queue successfully.');
    }
}
