<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Ne crée la colonne que si elle n'existe pas déjà
        Schema::table('issues', function (Blueprint $table) {
            if (!Schema::hasColumn('issues', 'is_published')) {
                $table->boolean('is_published')->default(false)->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            if (Schema::hasColumn('issues', 'is_published')) {
                $table->dropColumn('is_published');
            }
        });
    }
};
