<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Models\Income;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public static function show($limit = 25): \Illuminate\Http\JsonResponse
    {
        $id = Auth::id();

        $totalItems = Income::query()
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

    public function store(StoreIncomeRequest $request): \Illuminate\Http\JsonResponse
    {
        $id = Auth::id();

        $validated = $request->validated();
        $validated['user_id'] = $id;
        $income = Income::create($validated);

        return response()->json($income, 201);
    }

    public function update(UpdateIncomeRequest $request, Income $item): \Illuminate\Http\JsonResponse
    {
        $id = Auth::id();

        $validated = $request->validated();
        $validated['user_id'] = $id;

        $item->update($validated);

        return response()->json($item, 200);
    }

    public function destroy(Income $item): \Illuminate\Http\JsonResponse
    {
        $item->delete();

        return response()->json(null, 204);
    }
}
