<?php

namespace App\Jobs;

use App\Models\Receipts;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GeminiAPI\Laravel\Facades\Gemini;
use Exception;
use Illuminate\Support\Facades\Log;

class ProcessReceipt implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $receipt;

    public function __construct(Receipts $receipt)
    {
        $this->receipt = $receipt;
    }

    public function handle()
    {
        $filePath = storage_path('app/public/receipts/' . $this->receipt->image_path);
        $prompt = 'Привет! Как жизнь?';
        try {
            $response = Gemini::generateText(
                $prompt
            );

            Log::info('Receipt processing result: ' . $response);

            // TODO Реальная логика обработки чека
            // https://github.com/gemini-api-php/laravel
            $this->receipt->processed = true;
            $this->receipt->save();
        } catch (Exception $e) {
            logger('Error processing receipt: ' . $e->getMessage());

            $this->receipt->error = true;
            $this->receipt->save();
        }
    }
}
