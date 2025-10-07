<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            if (!Schema::hasColumn('issues', 'cover')) {
                $table->string('cover')->nullable()->after('date');        // chemin interne (storage/…)
            }
            if (!Schema::hasColumn('issues', 'summary')) {
                $table->text('summary')->nullable()->after('cover');       // sommaire (texte long)
            }
            if (!Schema::hasColumn('issues', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('summary');
            }
            if (!Schema::hasColumn('issues', 'published_at')) {
                $table->dateTime('published_at')->nullable()->after('is_published');
            }
        });
    }

    public function down(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            if (Schema::hasColumn('issues', 'published_at')) {
                $table->dropColumn('published_at');
            }
            if (Schema::hasColumn('issues', 'is_published')) {
                $table->dropColumn('is_published');
            }
            if (Schema::hasColumn('issues', 'summary')) {
                $table->dropColumn('summary');
            }
            if (Schema::hasColumn('issues', 'cover')) {
                $table->dropColumn('cover');
            }
        });
    }
};
