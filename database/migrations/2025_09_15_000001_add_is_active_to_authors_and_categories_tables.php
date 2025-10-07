<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajoute is_active sur authors si absent
        if (Schema::hasTable('authors') && ! Schema::hasColumn('authors', 'is_active')) {
            Schema::table('authors', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('slug');
            });
        }

        // Ajoute is_active sur categories si absent
        if (Schema::hasTable('categories') && ! Schema::hasColumn('categories', 'is_active')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('slug');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('authors') && Schema::hasColumn('authors', 'is_active')) {
            Schema::table('authors', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasTable('categories') && Schema::hasColumn('categories', 'is_active')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
