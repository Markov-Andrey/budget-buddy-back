<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategoriesAutoTableSeeder::class,
            CategoriesBadHabitsTableSeeder::class,
            CategoriesFixExpensesTableSeeder::class,
            CategoriesIncomeTableSeeder::class,
            CategoriesProductTableSeeder::class,
            CategoriesSpecialTableSeeder::class,
            DiscordMessageAcceptSeeder::class,
        ]);
    }
}
