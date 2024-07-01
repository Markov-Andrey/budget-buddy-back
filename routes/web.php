<?php

use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/test', function () {
    $client = new Client();

    $response = $client->request('GET', 'https://api.coingecko.com/api/v3/coins/bitcoin/market_chart', [
        'query' => [
            'vs_currency' => 'usd',
            'from' => '1711929600', // Unix timestamp начала периода (например, 30 января 2024 года)
            'to' => '1712275200',   // Unix timestamp конца периода (например, 4 февраля 2024 года)
            'interval' => 'daily',  // Интервал выборки (дневные данные)
            'days' => 5             // Количество дней данных (например, 5 дней)
        ],
        'headers' => [
            'accept' => 'application/json',
        ],
    ]);

    $data = json_decode($response->getBody(), true);

    $formattedData = [];

    foreach ($data['prices'] as $priceData) {
        $timestamp = $priceData[0] / 1000; // переводим миллисекунды в секунды
        $date = date('Y-m-d', $timestamp);
        $price = $priceData[1];

        $formattedData[] = [
            'Date' => $date,
            'Price' => $price,
        ];
    }

    // Вывод отформатированных данных
    return $formattedData;
});

Route::get('/', function () {
    return redirect('/admin'); // базовый рероут
});
