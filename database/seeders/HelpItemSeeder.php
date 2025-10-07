<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HelpItem;

class HelpItemSeeder extends Seeder
{
    public function run(): void
    {
        HelpItem::updateOrCreate(
            ['key' => 'faq'],
            [
                'group'        => 'subscribe',
                'title'        => 'Questions fréquentes',
                'content'      => "Comment accéder à mes numéros après l’achat ?\nPuis-je changer d’offre en cours d’abonnement ?\nComment recevoir une facture ?",
                'is_published' => true,
                'position'     => 1,
            ]
        );

        HelpItem::updateOrCreate(
            ['key' => 'info'],
            [
                'group'        => 'subscribe',
                'title'        => 'Informations utiles',
                'content'      => "Les offres donnent accès à l’édition numérique. La facturation est en FCFA. Pour toute question, contactez notre équipe.",
                'is_published' => true,
                'position'     => 2,
            ]
        );
    }
}
