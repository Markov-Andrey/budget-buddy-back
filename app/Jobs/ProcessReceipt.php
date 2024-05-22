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
        $prompt = 'Это чек?';
        try {
            $response = Gemini::generateTextUsingImage(
                'image/jpeg',
                base64_encode(file_get_contents(storage_path('app/public/receipts/' . $this->receipt->image_path))),
                $prompt,
            );

            dd($response);

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
