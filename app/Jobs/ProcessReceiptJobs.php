<?php

namespace App\Jobs;

use App\Models\ReceiptsData;
use App\Models\Receipts;
use App\Models\ReceiptsOrganization;
use App\Services\ApiResponseStabilizeService;
use DateTime;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GeminiAPI\Laravel\Facades\Gemini;
use Exception;
use Illuminate\Support\Facades\Log;

class ProcessReceiptJobs implements ShouldQueue
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
        $prompt = config('api.check_processing.prompt');
        try {
            $response = Gemini::generateTextUsingImageFile(
                'image/jpeg',
                $filePath,
                $prompt,
            );
            Log::info('API Receipt processing result: ' . $response);
            $defaultStructure = config('api.check_processing.default_structure');
            $data = ApiResponseStabilizeService::getInfo($response, $defaultStructure);
            $datetimeString = $data['data']['datetime'] ?? null;
            $datetime = DateTime::createFromFormat('Y-m-d H:i', $datetimeString);

            if (isset($data['data']['address']) && is_array($data['data']['address'])) {
                ReceiptsOrganization::create([
                    'receipts_id' => $this->receipt->id,
                    'name' => $data['data']['organization'] ?? null,
                    'city' => $data['data']['address']['city'] ?? null,
                    'street' => $data['data']['address']['street'] ?? null,
                    'entrance' => $data['data']['address']['entrance'] ?? null,
                    'datetime' => $datetime->format('Y-m-d H:i:s') ?? null,
                ]);
            }
            if (isset($data['data']['items']) && is_array($data['data']['items'])) {
                foreach ($data['data']['items'] as $item) {
                    if (isset($item)) {
                        ReceiptsData::create([
                            'receipts_id' => $this->receipt->id,
                            'name' => $item['name'] ?? null,
                            'quantity' => $item['quantity'] ?? null,
                            'weight' => $item['weight'] ?? null,
                            'price' => $item['price'] ?? null,
                        ]);
                    }
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