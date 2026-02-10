<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catFdb = Category::where('code', 'FDB')->firstOrFail();
        $catMatInf = Category::where('code', 'MAT-INF')->firstOrFail();
        $catCons = Category::where('code', 'CONS')->firstOrFail();

        Item::create([
            'ref' => 'STYLO-BLEU-001',
            'name' => 'Stylo Bille Bleu',
            'unit' => 'unité',
            'category_id' => $catFdb->id,
            'low_threshold' => 10,
            'safety_stock' => 5,
            'default_price' => 150.00,
        ]);

        Item::create([
            'ref' => 'PC-PORT-HP-005',
            'name' => 'Ordinateur Portable HP ProBook',
            'unit' => 'unité',
            'category_id' => $catMatInf->id,
            'low_threshold' => 2,
            'safety_stock' => 1,
            'default_price' => 450000.00,
        ]);

        Item::create([
            'ref' => 'TONER-LASER-30A',
            'name' => 'Cartouche Toner Laser 30A',
            'unit' => 'unité',
            'category_id' => $catCons->id,
            'low_threshold' => 3,
            'safety_stock' => 1,
            'default_price' => 35000.00,
        ]);

        Item::create([
            'ref' => 'CAHIER-A4-CARRE',
            'name' => 'Cahier A4 Grands Carreaux',
            'unit' => 'unité',
            'category_id' => $catFdb->id,
            'low_threshold' => 20,
            'safety_stock' => 10,
            'default_price' => 500.00,
        ]);
    }
}
