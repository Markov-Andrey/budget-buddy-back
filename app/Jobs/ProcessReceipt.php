<?php

namespace App\Jobs;

use App\Models\ReceiptsData;
use App\Models\Receipts;
use App\Services\ReceiptProcessingService;
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
        // https://github.com/gemini-api-php/laravel
        $filePath = storage_path('app/public/' . $this->receipt->image_path);
        $prompt = config('api.prompts.check_processing');
        try {
            $response = Gemini::generateTextUsingImageFile(
                'image/jpeg',
                $filePath,
                $prompt,
            );
            Log::info('API Receipt processing result: ' . $response);
            $data = ReceiptProcessingService::getInfo($response);

            if (isset($data['data']) && is_array($data['data']['address'])) {
                ReceiptsData::create([
                    'receipts_id' => $this->receipt->id,
                    'organization' => $data['organization'] ?? null,
                    'city' => $data['data']['address']['city'] ?? null,
                    'street' => $data['data']['address']['street'] ?? null,
                    'entrance' => $data['data']['address']['entrance'] ?? null,
                ]);
            }
            if (isset($data['data']['items']) && is_array($data['data']['items'])) {
                foreach ($data['data']['items'] as $item) {
                    ReceiptsData::create([
                        'receipts_id' => $this->receipt->id,
                        'name' => $item['organization'] ?? null,
                        'quantity' => $item['quantity'] ?? null,
                        'weight' => $item['weight'] ?? null,
                        'price' => $item['price'] ?? null,
                    ]);
                }
            }

            $this->receipt->processed = true;
            $this->receipt->error = $data['error'];
            $this->receipt->save();
        } catch (Exception $e) {
            logger('API Error processing receipt: ' . $e->getMessage());

            $this->receipt->processed = true;
            $this->receipt->error = true;
            $this->receipt->save();
        }
    }
}
