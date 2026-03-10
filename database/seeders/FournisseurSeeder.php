<?php

namespace Database\Seeders;

use App\Models\Fournisseur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FournisseurSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Fournisseur::create([
            'fou_nom' => 'Bureautique Distribution Dakar',
            'fou_contact_nom' => 'Awa Diop',
            'fou_email' => 'contact@bdd.sn',
            'fou_telephone' => '338202020',
            'fou_adresse' => '15 Avenue des Jambaars, Dakar'
        ]);
        Fournisseur::create([
            'fou_nom' => 'SEN-Informatique',
            'fou_contact_nom' => 'Cheikh Tidiane Sy',
            'fou_email' => 'commercial@sen-info.com',
            'fou_telephone' => '338606060',
            'fou_adresse' => 'Sicap Liberté 6, Dakar'
        ]);
        Fournisseur::create([
            'fou_nom' => 'La Papeterie Africaine',
            'fou_contact_nom' => 'Fatou Ndiaye',
            'fou_email' => 'info@papeterie-africaine.sn',
            'fou_telephone' => '338454545',
            'fou_adresse' => 'Rue 1 x 6, Médina, Dakar'
        ]);
    }
}

