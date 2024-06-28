<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\Investment;
use Illuminate\Support\Facades\Auth;

class InvestmentController
{
    public static function show($limit = 25): \Illuminate\Http\JsonResponse
    {
        $id = Auth::id();

        $totalItems = Investment::query()
            ->where('user_id', $id)
            ->count();

        $incomeData = Income::query()
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'incomeData' => $incomeData,
            'totalItems' => $totalItems,
        ]);
    }
}
