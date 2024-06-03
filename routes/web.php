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
    $return = (new App\Http\Controllers\DiscordController)->index();

    dd($return);

    return 'hello';
});

Route::get('/', function () {
    return redirect('/admin'); // базовый рероут
});
