<?php

namespace App\Services;

use App\Models\InvestmentPrices;
use App\Models\InvestmentType;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Получение данных валют и сохранение в БД (NBRB)
     * @param $currency
     * @return bool
     * @throws GuzzleException
     */
    public static function getCurrencyData($currency): bool
    {
        $client = new Client;
        $investmentType = InvestmentType::where('nbrb_id', $currency)->firstOrFail();

        // Найдем самую новую запись для данного инвестиционного типа
        $latestPrice = InvestmentPrices::where('investment_type_id', $investmentType->id)
            ->orderBy('date', 'desc')
            ->first();

        $now = Carbon::now();

        // Если последняя запись существует и обновлена сегодня, логируем и выходим
        if ($latestPrice && Carbon::parse($latestPrice->date)->isToday()) {
            Log::info("Data for {$currency} is already up-to-date.");
            return true;
        }

        // Определяем даты для запроса
        $startDate = $latestPrice ? Carbon::parse($latestPrice->date)->addDay() : $now->subDays(365);
        $endDate = $now;

        // Если startDate больше endDate, данные обрабатывать нельзя
        if ($startDate->greaterThan($endDate)) {
            Log::warning("Cannot fetch data for {$currency} as the start date is after the end date.");
            return false;
        }

        // Форматируем даты для запроса
        $startDateFormatted = $startDate->format('Y-m-d');
        $endDateFormatted = $endDate->format('Y-m-d');

        $response = $client->request('GET', env('NBRB_API_URL') . "ExRates/Rates/Dynamics/{$currency}", [
            'query' => [
                'startDate' => $startDateFormatted,
                'endDate' => $endDateFormatted,
            ],
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        foreach ($data as $priceData) {
            $date = Carbon::parse($priceData['Date'])->format('Y-m-d');
            $price = $priceData['Cur_OfficialRate'];

            // Сохраняем данные в таблицу investment_prices, если их нет
            InvestmentPrices::updateOrCreate(
                [
                    'investment_type_id' => $investmentType->id,
                    'date' => $date,
                    'price' => $price,
                    'currency_type' => 'BYN',
                ]
            );
        }
        return true;
    }
}
