<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Income;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public static function index()
    {
        $user = Auth::user();
        $subcategories = Subcategory::select('subcategories.id', 'subcategories.name')
            ->join('categories', 'subcategories.category_id', '=', 'categories.id')
            ->where('categories.name', 'Доход')
            ->get();

        $incomes = Income::where('user_id', $user->id)->paginate(10);
        return response()->json([
            'incomes' => $incomes,
            'subcategories' => $subcategories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'amount' => 'required|numeric',
        ]);

        $income = Income::create($validated);
        return response()->json($income, 201);
    }

    public function show($id)
    {
        $income = Income::findOrFail($id);
        return response()->json($income);
    }

    public function update(Request $request, $id)
    {
        $income = Income::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'subcategory_id' => 'sometimes|required|exists:subcategories,id',
            'amount' => 'sometimes|required|numeric',
        ]);
        $income->update($validated);

        return response()->json($income);
    }

    // Удаление элемента
    public function destroy(Income $income)
    {
        $income->delete();

        return response()->json(null, 204);
    }
}
