<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentDetails;
use Illuminate\Support\Facades\Auth;

class InvestmentController
{
    public static function show(): \Illuminate\Http\JsonResponse
    {
        $id = Auth::id();
        $sumInvestmentData = 0; // Инициализация переменной
        $sumInvestmentCurrentData = 0; // Инициализация переменной

        $investmentData = InvestmentDetails::getInvestmentDetailsData($id);
        foreach ($investmentData as $data) {
            $sumInvestmentData += $data['total_value'];
            $sumInvestmentCurrentData += $data['latest_amount'];
        }

        return response()->json([
            'investmentData' => $investmentData,
            'sumInvestmentData' => round($sumInvestmentData, 2),
            'sumInvestmentCurrentData' => round($sumInvestmentCurrentData, 2),
        ]);
    }

    public static function getInvest($limit = 25): \Illuminate\Http\JsonResponse
    {
        $id = Auth::id();

        $data = Investment::query()
            ->with('investmentDetail')
            ->where('user_id', '=', $id)
            ->limit($limit)
            ->get();

        $totalItems = Investment::query()
            ->with('investmentDetail')
            ->where('user_id', '=', $id)
            ->count();

        return response()->json([
            'investData' => $data,
            'totalItems' => $totalItems,
        ]);
    }
}
