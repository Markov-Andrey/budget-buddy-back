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
            "Бензин, дизель, газ",
            "Запчасти авто",
        ];
        $subcategories = [];

        foreach ($subcategoryNames as $name) {
            $subcategories[] = [
                'name' => $name,
                'category_id' => $categoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Subcategory::insert($subcategories);
    }
}
