<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('issues', function (Blueprint $table) {
            if (!Schema::hasColumn('issues', 'number'))       $table->unsignedInteger('number')->default(1)->index();
            if (!Schema::hasColumn('issues', 'date'))         $table->date('date')->nullable()->index();
            if (!Schema::hasColumn('issues', 'is_published')) $table->boolean('is_published')->default(false)->index();
            if (!Schema::hasColumn('issues', 'cover'))        $table->string('cover')->nullable();
            if (!Schema::hasColumn('issues', 'cover_disk'))   $table->string('cover_disk')->nullable()->default('public');
            if (!Schema::hasColumn('issues', 'summary'))      $table->json('summary')->nullable(); // TEXT sous SQLite : OK
        });

        if (Schema::hasColumn('issues', 'is_published')) {
            DB::table('issues')->whereNull('is_published')->update(['is_published' => false]);
        }
        if (Schema::hasColumn('issues', 'cover_disk')) {
            DB::table('issues')->whereNull('cover_disk')->update(['cover_disk' => 'public']);
        }
    }

    public function down(): void
    {
        // no-op pour éviter perte de données
    }
};
