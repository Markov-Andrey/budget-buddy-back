<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class CategoriesSpecialTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            'Техника и электроника',
            'Косметика и парфюмерия',
            'Здоровье и лекарства',
            'Бытовая химия',
            'Интимные товары',
            'Одежда и обувь',
            'Творчество, развлечения и игрушки',
            'Ювелирные изделия',
            'Электронные товары',
            'Абонементы',
            'Мебель',
            'Подарки',
            'Канцелярия и печатная продукция',
        ];

        foreach ($names as $name) {
            $category = Category::create(['name' => $name]);
            Subcategory::create([
                'name' => $name,
                'category_id' => $category->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
    }
}
