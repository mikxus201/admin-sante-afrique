<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // Rôles
        $viewer      = Role::firstOrCreate(['name' => 'viewer']);
        $moderator   = Role::firstOrCreate(['name' => 'moderator']);
        $admin       = Role::firstOrCreate(['name' => 'admin']);
        $superAdmin  = Role::firstOrCreate(['name' => 'super-admin']);

        // Utilisateurs par défaut (si non existants)
        $sa = User::firstOrCreate(
            ['email' => 'super@sante-afrique.test'],
            ['name' => 'Super Admin', 'password' => Hash::make('password')]
        );
        $ad = User::firstOrCreate(
            ['email' => 'admin@sante-afrique.test'],
            ['name' => 'Admin Santé Afrique', 'password' => Hash::make('password')]
        );
        $mo = User::firstOrCreate(
            ['email' => 'moderateur@sante-afrique.test'],
            ['name' => 'Modérateur', 'password' => Hash::make('password')]
        );

        // Attribution des rôles (idempotent)
        $sa->syncRoles(['super-admin']);
        $ad->syncRoles(['admin']);
        $mo->syncRoles(['moderator']);
    }
}
