<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CategoriesProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::create(['name' => 'Продукты']);
        $categoryId = $category->id;

        $subcategoryNames = [
            "Мясная продукция",
            "Молочная продукция",
            "Рыба и морепродукты",
            "Фрукты",
            "Овощи",
            "Хлебобулочные изделия",
            "Напитки",
            "Сладости и десерты",
            "Бакалея",
            "Замороженные продукты",
            "Готовые блюда и полуфабрикаты",
            "Яйца и яичные продукты",
            "Детское питание",
            "Диетические и специализированные продукты",
            "Орехи и семена",
            "Грибы",
            "Специи и приправы",
            "Масла и жиры",
            "Соусы и маринады",
            "Консервированные продукты",
            "Снеки и закуски"
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
