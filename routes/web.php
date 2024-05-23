<?php

use App\Models\ReceiptsData;
use App\Models\ReceiptsOrganization;
use App\Services\ReceiptProcessingService;
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
    $response = '
    {
        "organization": "Санта Ритейл",
        "address": {
            "city": "Минск",
            "street": "Володько",
            "entrance": "9, пом. 6"
        },
        "items": [
            {
                "name": "Карамель Чупа Чупс Ассорти",
                "quantity": 1,
                "weight": 0.012,
                "price": 0.53
            },
            {
                "name": "Коктейль молочный ТОП карамельный",
                "quantity": 1,
                "weight": 0.45,
                "price": 2.55
            },
            {
                "name": null,
                "quantity": 1,
                "weight": 0,
                "price": null
            }
        ]
    }
    ';
    $data = ReceiptProcessingService::getInfo($response);
    return dd($data);
});

