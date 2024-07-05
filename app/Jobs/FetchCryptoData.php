<?php

namespace App\Jobs;

use App\Models\InvestmentType;
use App\Services\CryptoService;
use GuzzleHttp\Exception\ClientException;
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
        } catch (ClientException $e) {
            $statusCode = $e->getCode();
            Log::error("Error fetching data for crypto {$this->crypto}: " . $e->getMessage());

            if ($statusCode === 429) {
                $delay = now()->addSeconds(5);
                dispatch(new FetchCryptoData($this->crypto))->delay($delay);
            }
        } catch (\Exception $e) {
            Log::error("Unexpected error fetching data for crypto {$this->crypto}: " . $e->getMessage());
        }
    }
}
