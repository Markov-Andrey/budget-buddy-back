<?php

namespace App\Console\Commands;

use App\Jobs\DiscordProcessJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DiscordAdd extends Command
{
    protected $signature = 'discord:add';
    protected $description = 'Обработка команд из Discord';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $discordJob = new DiscordProcessJob();
        $discordJob->handle();

        $this->info('Discord jobs processed successfully.');
        Log::channel('discord')->info('Discord jobs processed successfully.');
    }
}
