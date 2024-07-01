<?php

namespace App\Services;

use App\Models\InvestmentPrices;
use App\Models\InvestmentType;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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

        // Если записи нет, используем 365 дней
        if ($latestPrice) {
            $lastDate = Carbon::parse($latestPrice->date);
            $now = Carbon::now();
            $days = $lastDate->diffInDays($now);
        } else {
            $days = 365;
        }

        $response = $client->request('GET', env('COINGECKO_API_URL')."coins/{$crypto}/market_chart", [
            'query' => [
                'vs_currency' => 'usd',
                'interval' => 'daily',
                'days' => $days
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

            // Сохраняем данные в таблицу investment_prices
            InvestmentPrices::create([
                'investment_type_id' => $investmentType->id,
                'date' => $date,
                'price' => $price,
            ]);
        }
        return true;
    }
}
