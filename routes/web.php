<?php

use App\Http\Controllers\IncomeController;
use Illuminate\Support\Facades\Route;

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
    $test1 = \App\Models\DiscordMessage::getRandomMessageByCode('accept');
    dd($test1);

    return '+';
});

Route::get('/', function () {
    return redirect('/admin'); // базовый рероут
});
