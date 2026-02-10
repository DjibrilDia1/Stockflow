<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::create([
            'name' => 'Bureautique Distribution Dakar',
            'contact_name' => 'Awa Diop',
            'email' => 'contact@bdd.sn',
            'phone' => '338202020',
            'address' => '15 Avenue des Jambaars, Dakar'
        ]);
        Supplier::create([
            'name' => 'SEN-Informatique',
            'contact_name' => 'Cheikh Tidiane Sy',
            'email' => 'commercial@sen-info.com',
            'phone' => '338606060',
            'address' => 'Sicap Liberté 6, Dakar'
        ]);
        Supplier::create([
            'name' => 'La Papeterie Africaine',
            'contact_name' => 'Fatou Ndiaye',
            'email' => 'info@papeterie-africaine.sn',
            'phone' => '338454545',
            'address' => 'Rue 1 x 6, Médina, Dakar'
        ]);
    }
}
