<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CategoriesAutoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::create(['name' => 'Автомобиль']);
        $categoryId = $category->id;

        $subcategoryNames = [
            "Топливо" => 1,
            "Ремонт авто" => 0,
            "Плановое ТО" => 0,
            "Страховка" => 0,
            "Расходники" => 0,
            "Доработки" => 0,
        ];
        $subcategories = [];

        foreach ($subcategoryNames as $name => $check) {
            $subcategories[] = [
                'name' => $name,
                'category_id' => $categoryId,
                'is_check' => $check ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Subcategory::insert($subcategories);
    }
}
