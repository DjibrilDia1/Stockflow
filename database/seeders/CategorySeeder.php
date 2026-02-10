<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name' => 'Fournitures de bureau', 'code' => 'FDB']);
        Category::create(['name' => 'Matériel Informatique', 'code' => 'MAT-INF']);
        Category::create(['name' => 'Consommables', 'code' => 'CONS']);
    }
}
