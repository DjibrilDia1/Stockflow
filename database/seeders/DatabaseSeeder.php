<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
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
        // ——— Données de base ———
        $this->call([
            CategorieSeeder::class,
            EntrepotSeeder::class,
            FournisseurSeeder::class,
            ServiceSeeder::class,
            ArticleSeeder::class,
            StockArticleSeeder::class,
            MouvementStockSeeder::class,
        ]);

        // ——— Récupération des services créés ———
        $serviceCom  = Service::where('ser_code', 'COM')->first();
        $serviceTech = Service::where('ser_code', 'TECH')->first();
        $serviceProd = Service::where('ser_code', 'PROD')->first();

        // ——— Gestionnaire (sans service) ———
        User::updateOrCreate(
            ['email' => 'Moussa@stockflow.sn'],
            [
                'name'     => 'Moussa',
                'password' => bcrypt('password123'),
                'role'     => 'gestionnaire',
                'ser_id'   => null,
            ]
        );

        // ——— Demandeurs par service ———
        User::updateOrCreate(
            ['email' => 'djibson494@gmail.com'],
            [
                'name'     => 'Djibril',
                'password' => bcrypt('passer123'),
                'role'     => 'demandeur',
                'ser_id'   => $serviceCom?->ser_id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'tech.agent@stockflow.sn'],
            [
                'name'     => 'Amadou Diallo',
                'password' => bcrypt('password123'),
                'role'     => 'demandeur',
                'ser_id'   => $serviceTech?->ser_id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'prod.agent@stockflow.sn'],
            [
                'name'     => 'Fatou Sow',
                'password' => bcrypt('password123'),
                'role'     => 'demandeur',
                'ser_id'   => $serviceProd?->ser_id,
            ]
        );
    }
}