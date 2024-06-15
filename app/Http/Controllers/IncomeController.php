<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncomeRequest;
use App\Http\Requests\UpdateIncomeRequest;
use App\Models\Income;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public static function index(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $subcategories = Subcategory::query()
            ->select('subcategories.id', 'subcategories.name')
            ->join('categories', 'subcategories.category_id', '=', 'categories.id')
            ->where('categories.name', 'Доход')
            ->get();

        $incomes = Income::query()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return response()->json([
            'incomes' => $incomes,
            'subcategories' => $subcategories,
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
