<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('articles') && !Schema::hasColumn('articles', 'rubric_id')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->unsignedBigInteger('rubric_id')->nullable()->after('category_id');
                $table->foreign('rubric_id')->references('id')->on('rubrics')->nullOnDelete();
                $table->index('rubric_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('articles') && Schema::hasColumn('articles', 'rubric_id')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropForeign(['rubric_id']);
                $table->dropColumn('rubric_id');
            });
        }
    }
};
