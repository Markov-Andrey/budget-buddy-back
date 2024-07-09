<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\InvestmentController;
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
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});

Route::get('hello', function () {
    return response()->json(['message' => 'Hello!']);
});

Route::prefix('receipts')->group(function () {
    Route::post('/add', [ReceiptsController::class, 'store'])->middleware('auth:sanctum');
    Route::post('/show', [ReceiptsController::class, 'show'])->middleware('auth:sanctum');
    Route::delete('/delete/{id}', [ReceiptsController::class, 'delete'])->middleware('auth:sanctum');
});

Route::prefix('income')->group(function () {
    Route::get('/show/{limit}', [IncomeController::class, 'show'])->middleware('auth:sanctum');
    Route::post('/store', [IncomeController::class, 'store'])->middleware('auth:sanctum');
    Route::put('/update/{item}', [IncomeController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/delete/{item}', [IncomeController::class, 'destroy'])->middleware('auth:sanctum');
});

Route::prefix('investment')->group(function () {
    Route::get('/show', [InvestmentController::class, 'show'])->middleware('auth:sanctum');
});

Route::prefix('category')->group(function () {
    Route::get('/get-income', [CategoryController::class, 'getIncome']);
});

Route::prefix('info')->group(function () {
    Route::get('/balance', [InfoController::class, 'balance'])->middleware('auth:sanctum');
    Route::get('/personal', [InfoController::class, 'personal'])->middleware('auth:sanctum');
    Route::get('/running-costs', [InfoController::class, 'runningCosts'])->middleware('auth:sanctum');
});
