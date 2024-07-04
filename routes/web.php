<?php

use App\Models\InvestmentType;
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
    $test = InvestmentType::query()->pluck('coingecko_id')->toArray();
    dd($test);

    return $test;
});

Route::get('/cryptos', function () {
    $client = new Client();

    $response = $client->request('GET', 'https://api.coingecko.com/api/v3/coins/list', [
        'headers' => [
            'accept' => 'application/json',
        ],
    ]);

    $data = json_decode($response->getBody(), true);

    // Вывод списка доступных криптовалют
    return $data;
});

Route::get('/', function () {
    return redirect('/admin'); // базовый рероут
});
