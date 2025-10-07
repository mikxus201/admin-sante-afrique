<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('issues', function (Blueprint $table) {
            if (!Schema::hasColumn('issues', 'cover'))   $table->string('cover')->nullable();
            if (!Schema::hasColumn('issues', 'summary')) $table->json('summary')->nullable();
        });
    }
    public function down(): void {
        Schema::table('issues', function (Blueprint $table) {
            if (Schema::hasColumn('issues', 'summary')) $table->dropColumn('summary');
            if (Schema::hasColumn('issues', 'cover'))   $table->dropColumn('cover');
        });
    }
};
