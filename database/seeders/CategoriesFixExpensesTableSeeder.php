<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CategoriesFixExpensesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::create(['name' => 'Постоянные']);
        $categoryId = $category->id;

        $subcategoryNames = [
            "Постоянные расходы",
            "Коммунальные услуги",
            "Электричество",
            "Интернет",
            "Мобильная связь",
            "Кредиты/рассрочки",
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
