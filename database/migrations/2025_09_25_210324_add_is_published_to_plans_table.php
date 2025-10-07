<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ajoute la colonne si absente
        Schema::table('plans', function (Blueprint $table) {
            if (! Schema::hasColumn('plans', 'is_published')) {
                $table->boolean('is_published')->default(false);
            }
        });

        // Si une ancienne colonne is_active existe, on copie sa valeur dans is_published
        if (Schema::hasColumn('plans', 'is_active') && Schema::hasColumn('plans', 'is_published')) {
            DB::table('plans')->update([
                'is_published' => DB::raw('CASE WHEN is_active = 1 THEN 1 ELSE 0 END')
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'is_published')) {
                $table->dropColumn('is_published');
            }
        });
    }
};
