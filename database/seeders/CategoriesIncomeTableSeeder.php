<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CategoriesIncomeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::create(['name' => 'Доход']);
        $categoryId = $category->id;

        $subcategoryNames = [
            "Заработная плата",
            "Подработка",
            "Пассивный доход",
            "Сдача в аренду",
            "Разовый доход",
            "Продажа б/у",
        ];
        $subcategories = [];

        foreach ($subcategoryNames as $name) {
            $subcategories[] = [
                'name' => $name,
                'category_id' => $categoryId,
                'is_check' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Subcategory::insert($subcategories);
    }
}
