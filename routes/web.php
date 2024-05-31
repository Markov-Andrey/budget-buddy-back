<?php

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

Route::get('/hello', function () {
    $test1 = \App\Models\Income::averageMonthlyLastYear(1);
    $test2 = \App\Models\Receipts::averageMonthlyLastYear(1);
    dd($test1, $test2);

    return 'hello';
});

Route::get('/', function () {
    return redirect('/admin'); // базовый рероут
});
