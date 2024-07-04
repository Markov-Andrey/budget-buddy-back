<?php

namespace App\Services;

use App\Models\InvestmentPrices;
use App\Models\InvestmentType;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class CryptoService
{
    /**
     * Получение данных крипты и сохранение в БД
     * @param $crypto
     * @return bool
     * @throws GuzzleException
     */
    public static function getCryptoData($crypto): bool
    {
        $client = new Client;
        $investmentType = InvestmentType::where('coingecko_id', $crypto)->firstOrFail();

        // Найдем самую новую запись для данного инвестиционного типа
        $latestPrice = InvestmentPrices::where('investment_type_id', $investmentType->id)
            ->orderBy('date', 'desc')
            ->first();

        $now = Carbon::now();
        $days = 365; // По умолчанию получаем данные за 365 дней

        if ($latestPrice) {
            $lastDate = Carbon::parse($latestPrice->date);

            // Проверим, если данные обновлялись сегодня, запрос не требуется
            if ($lastDate->isToday()) {
                Log::info("Data for {$crypto} is already up-to-date.");
                return true;
            }

            $days = $lastDate->diffInDays($now);
        }

        $response = $client->request('GET', env('COINGECKO_API_URL') . "coins/{$crypto}/market_chart", [
            'query' => [
                'vs_currency' => 'usd',
                'interval' => 'daily',
                'days' => $days,
                'currency_type' => 'USD',
            ],
            'headers' => [
                'accept' => 'application/json',
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        foreach ($data['prices'] as $priceData) {
            $timestamp = $priceData[0] / 1000; // переводим миллисекунды в секунды
            $date = date('Y-m-d', $timestamp);
            $price = $priceData[1];

            // Сохраняем данные в таблицу investment_prices, если их нет
            InvestmentPrices::updateOrCreate(
                ['investment_type_id' => $investmentType->id, 'date' => $date],
                ['price' => $price]
            );
        }
        return true;
    }
}
