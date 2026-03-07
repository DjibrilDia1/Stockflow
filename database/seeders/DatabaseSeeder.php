<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Moussa Fall',
        //     'email' => 'moussa.fall@example.com',
        // ]);

        $this->call([
            CategorieSeeder::class,
            EntrepotSeeder::class,
            FournisseurSeeder::class,
            ServiceSeeder::class,
            ArticleSeeder::class,
            StockArticleSeeder::class,
            MouvementStockSeeder::class,
        ]);
    }
}

