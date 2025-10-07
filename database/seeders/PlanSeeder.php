<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(
            ['slug' => 'annuel-print-digital'],
            ['name' => 'Abonnement Annuel (Papier + Numérique)',
             'description' => '06 numéros papier + accès illimité au site', 
             'price_fcfa' => 90000, 
             'features'=>['06 numéros papier','Accès articles premium','Offres d’emploi incluses','Newsletter pro'],
             'is_published' => true]
        );

        Plan::updateOrCreate(
            ['slug' => 'annuel-entreprise'],
            ['name' => 'Offre Annuelle Papier', 
            'description' => '06 numéros (édition papier)', 
            'price_fcfa' => 50000, 
            'features'=>['06 numéros papier','livrer à votre adresse','Newsletter pro'],
            'is_published' => true]
        );

        Plan::updateOrCreate(
            ['slug' => 'annuel-numerique'],
            ['name' => 'Offre Annuelle Numérique', 
            'description' => 'Accès illimité aux contenus premium + archives', 
            'price_fcfa' => 15000, 
            'features'=>['Accès premium','Accès archivre ','Newsletter pro'],
            'is_published' => true]
        );
    }
}
