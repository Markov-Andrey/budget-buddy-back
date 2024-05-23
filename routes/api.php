<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReceiptsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Маршрут для регистрации пользователя
Route::post('/register', [AuthController::class, 'register']);
// Маршрут для аутентификации пользователя
Route::post('/login', [AuthController::class, 'login']);

Route::get('hello', function () {
    return response()->json(['message' => 'Hello!']);
});
Route::post('/images', [ReceiptsController::class, 'store']);
