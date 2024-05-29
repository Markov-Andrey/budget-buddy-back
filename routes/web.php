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
    $datetimeString = "2023-05-29 08:30:36";
    $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $datetimeString);

    // Проверка, удалось ли преобразовать строку в объект DateTime
    if ($datetime === false) {
        // Если не удалось, используем текущее время
        $datetime = new DateTime('now', new DateTimeZone('UTC'));
    }

    // Преобразование в UNIX-время
    $unixTimestamp = $datetime->getTimestamp();

    // Выводим результат
    dd($datetime);

    return 'hello';
});
Route::get('/', function () {
    return redirect('/admin'); // базовый рероут
});
