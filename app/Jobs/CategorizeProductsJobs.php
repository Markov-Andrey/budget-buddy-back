<?php

namespace App\Jobs;

use App\Http\Controllers\GeminiController;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Receipts;
use App\Models\ReceiptsData;
use App\Models\Subcategory;
use App\Services\ApiResponseStabilizeService;
use Illuminate\Support\Facades\Log;

class CategorizeProductsJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $receipt;

    /**
     * Create a new job instance.
     */
    public function __construct(Receipts $receipt)
    {
        $this->receipt = $receipt;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $id = $this->receipt->id;
        $prompt = config('api.check_subcategories.prompt');

        // список субкатегорий для запроса
        $subcategories = Subcategory::query()->where('is_check', 1)->select('id', 'name')->get();
        $subcategoriesString = "\nСубкатегории: " . $subcategories->pluck('name')->implode(', ');
        $subcategoriesIdArray = $subcategories->pluck('id', 'name')->toArray();

        $products = Receipts::with('data')->find($id);

        if ($products === null || $products->data->isEmpty()) {
            Log::channel('gemini')->warning('Чек с ID ' . $id . ' не содержит данных для анализа.');
            return;
        }

        $productsString = "\nПродукты для анализа: " . $products->data->pluck('name')->implode(', ');
        $productsArray = $products->data->pluck('id', 'name')->toArray();

        $answer = $prompt . $subcategoriesString . $productsString;

        try {
            $response = GeminiController::generateText($answer);
            Log::channel('gemini')->info('API Categorize Products result: ' . $response);
            $defaultStructure = config('api.check_subcategories.default_structure');
            $data = ApiResponseStabilizeService::getInfo($response, $defaultStructure);
            $stabilizeData = $data['data']['data'];

            foreach ($stabilizeData as $item) {
                $subcategory_id = $subcategoriesIdArray[$item['category']] ?? null;
                $product_id = $productsArray[$item['name']] ?? null;

                $product = ReceiptsData::find($product_id);
                if ($product) {
                    $product->subcategory_id = $subcategory_id;
                    $product->save();
                }
            }
            Log::channel('gemini')->info('API Categorize Products successful');
        } catch (Exception $e) {
            Log::channel('gemini')->error('API Categorize Products: ' . $e->getMessage());
        }
    }
}
