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
            "Яйца и яичные продукты",
            "Рыба и морепродукты",
            "Хлебобулочные изделия",
            "Овощи, фрукты, ягоды",
            "Чай и кофе",
            "Напитки",
            "Сладости и десерты",
            "Бакалея",
            "Готовые блюда и полуфабрикаты",
            "Консервированные продукты",
            "Детское питание",
            "Диетические и специализированные продукты",
            "Орехи и семена",
            "Специи и приправы",
            "Масла и жиры",
            "Соусы и маринады",
            "Снеки и закуски"
        ];
        $subcategories = [];

        foreach ($subcategoryNames as $name) {
            $subcategories[] = [
                'name' => $name,
                'category_id' => $categoryId,
                'is_check' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Subcategory::insert($subcategories);
    }
}
