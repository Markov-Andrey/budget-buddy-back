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

    protected $crypto;

    public function __construct($crypto)
    {
        $this->crypto = $crypto;
    }

    public function handle(): void
    {
        try {
            CryptoService::getCryptoData($this->crypto);
        } catch (\Exception $e) {
            Log::error("Error fetching data for crypto {$this->crypto}: " . $e->getMessage());

            // TODO ошибка осталась!!!
            // Повторная отправка задачи в очередь при ошибке "too many requests"
            if ($e->getMessage() === '429 Too Many Requests') {
                $delay = now()->addSeconds(5);
                self::dispatch($this->crypto)->delay($delay);
            }
        }
    }
}
