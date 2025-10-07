<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('description');
            }
        });

        // Copier l’ancienne colonne si elle existe
        if (Schema::hasColumn('categories', 'active')) {
            DB::table('categories')->update([
                'is_active' => DB::raw("CASE WHEN active = 1 THEN 1 ELSE 0 END"),
            ]);

            // (facultatif) supprimer 'active' si supporté par ton SGBD
            Schema::table('categories', function (Blueprint $table) {
                if (Schema::hasColumn('categories', 'active')) {
                    $table->dropColumn('active');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'active')) {
                $table->boolean('active')->default(true)->after('description');
            }
        });

        // Copier en sens inverse
        if (Schema::hasColumn('categories', 'is_active')) {
            DB::table('categories')->update([
                'active' => DB::raw("CASE WHEN is_active = 1 THEN 1 ELSE 0 END"),
            ]);

            Schema::table('categories', function (Blueprint $table) {
                if (Schema::hasColumn('categories', 'is_active')) {
                    $table->dropColumn('is_active');
                }
            });
        }
    }
};
