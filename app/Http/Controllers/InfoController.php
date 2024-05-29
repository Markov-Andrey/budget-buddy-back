<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Receipts;
use Illuminate\Support\Facades\Auth;

class InfoController extends Controller
{
    public function balance()
    {
        $user = Auth::user();

        $income = number_format(Income::totalIncomeUser($user->id), 2);
        $loss = number_format(Receipts::totalLossUser($user->id), 2);
        $balance = number_format($income - $loss, 2);

        return response()->json([
            'income' => $income,
            'loss' => $loss,
            'balance' => $balance,
        ], 200);
    }
}
