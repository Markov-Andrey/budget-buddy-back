<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;

class CategoryController extends Controller
{
    public static function getIncome(): \Illuminate\Http\JsonResponse
    {
        $subcategories = Subcategory::query()
            ->select('subcategories.id', 'subcategories.name')
            ->join('categories', 'subcategories.category_id', '=', 'categories.id')
            ->where('categories.name', 'Доход')
            ->get();

        return response()->json($subcategories, 200);
    }
}
