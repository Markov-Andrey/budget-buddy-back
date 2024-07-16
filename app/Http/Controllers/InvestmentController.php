<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\InvestmentDetails;
use App\Models\InvestmentType;
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

    public static function getInvest($limit = 25)
    {
        $id = Auth::id();
        $investData = Investment::query()
            ->with([
                'investmentDetail',
                'investmentDetail.investmentType',
            ])
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $totalItems = Investment::query()
            ->where('user_id', $id)
            ->count();

        return response()->json([
            'investData' => $investData,
            'totalItems' => $totalItems,
        ]);
    }

    public static function getCrypto(): \Illuminate\Database\Eloquent\Collection|array
    {
        return InvestmentType::query()->select(['id', 'name', 'code'])->get();
    }

    public static function update(Investment $item)
    {
        $request = request()->all(); // Получаем все данные из запроса
        // Обновление основной модели Investment
        $item->update([
            'total_amount' => $request['total_amount'],
            'created_at' => $request['created_at'],
        ]);

        // Обновление связанных деталей InvestmentDetail
        foreach ($request['investment_detail'] as $detailData) {
            $detail = InvestmentDetails::find($detailData['id']);

            if ($detail) {
                $detail->update([
                    'investment_type_id' => $detailData['investment_type_id'],
                    'size' => $detailData['size'],
                    'cost_per_unit' => $detailData['cost_per_unit'],
                ]);
            }
        }

        // Возвращаем успешный ответ с обновленными данными
        return response()->json(['message' => 'Данные успешно обновлены'], 200);
    }
}
