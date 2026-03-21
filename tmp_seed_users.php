<?php

use App\Models\User;
use App\Models\Service;

$com  = Service::where('ser_code', 'COM')->first();
$tech = Service::where('ser_code', 'TECH')->first();
$prod = Service::where('ser_code', 'PROD')->first();

// Rattacher Djibril au service Commercial
User::where('email', 'djibson494@gmail.com')->update(['ser_id' => $com?->ser_id]);

// Créer un demandeur pour le Service Technique
User::updateOrCreate(
    ['email' => 'tech.agent@stockflow.sn'],
    [
        'name'     => 'Amadou Diallo',
        'password' => bcrypt('password123'),
        'role'     => 'demandeur',
        'ser_id'   => $tech?->ser_id,
    ]
);

// Créer une demandeuse pour le Service Production
User::updateOrCreate(
    ['email' => 'prod.agent@stockflow.sn'],
    [
        'name'     => 'Fatou Sow',
        'password' => bcrypt('password123'),
        'role'     => 'demandeur',
        'ser_id'   => $prod?->ser_id,
    ]
);

echo "Utilisateurs rattachés aux services : COM({$com?->ser_id}) TECH({$tech?->ser_id}) PROD({$prod?->ser_id})\n";
