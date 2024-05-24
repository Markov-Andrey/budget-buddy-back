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

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('hello', function () {
    return response()->json(['message' => 'Hello!']);
});
Route::prefix('receipts')->group(function () {
    Route::post('/add', [ReceiptsController::class, 'store'])->middleware('auth:sanctum');
});
