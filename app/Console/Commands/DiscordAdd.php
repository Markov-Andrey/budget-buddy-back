<?php

namespace App\Console\Commands;

use App\Jobs\DiscordProcessJob;
use Illuminate\Console\Command;

class DiscordAdd extends Command
{
    protected $signature = 'discord:add';
    protected $description = 'Обработка новых изображений из Discord';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DiscordProcessJob::dispatch();

        $this->info('Discord jobs processed successfully.');
    }
}
