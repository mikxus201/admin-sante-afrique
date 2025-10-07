<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Author;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {    

        // —— Comptes de base
        $admin = User::firstOrCreate(
            ['email' => 'admin@sante-afrique.test'],
            [
                'name' => 'Admin Santé Afrique',
                'password' => Hash::make('password'), // change-le ensuite !
            ]
        );

        $mod = User::firstOrCreate(
            ['email' => 'moderateur@sante-afrique.test'],
            [
                'name' => 'Modérateur',
                'password' => Hash::make('password'), // change-le ensuite !
            ]
        );

        // Si tu as un champ "role" (varchar) sur users, on peut le remplir :
        if (User::query()->where('id', $admin->id)->exists() && !isset($admin->role)) {
            // ignore si pas de colonne role
        } else {
            // si colonne "role" existe :
            $admin->forceFill(['role' => 'admin'])->save();
            $mod->forceFill(['role' => 'moderator'])->save();
        }

        // —— Catégories de base (si tu utilises App\Models\Category)
        if (class_exists(Category::class)) {
            foreach ([
                'Actualités',
                'Conseils pratiques',
                'Les ODD',
                'Santé Mentale',
                'Équité & Accès aux produits de santé',
                'Business Santé',
            ] as $name) {
                Category::firstOrCreate(
                    ['name' => $name],
                    ['is_active' => true]
                );
            }
        }

        // —— Auteur de base (si tu utilises App\Models\Author)
        if (class_exists(Author::class)) {
            Author::firstOrCreate(
                ['name' => 'Rédaction Santé Afrique'],
                [
                    'bio' => "La rédaction de Santé Afrique publie analyses, enquêtes et dossiers de référence.",
                    'is_active' => true,
                ]
            );
        }
        // Mets ici tes seeders existants si besoin (Categories, Authors, Issues, etc.)
        $this->call([
            RoleSeeder::class, // <— AJOUT : crée rôles + admin
            // CategorySeeder::class,
            // AuthorSeeder::class,
            // IssueSeeder::class,
        ]);

        // —— Optionnel : fabrique quelques utilisateurs de test
        // \App\Models\User::factory(5)->create();
    }
}
