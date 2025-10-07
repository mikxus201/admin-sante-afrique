<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('articles') && !Schema::hasColumn('articles', 'previous_slugs')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->text('previous_slugs')->nullable()->after('slug');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('articles') && Schema::hasColumn('articles', 'previous_slugs')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropColumn('previous_slugs');
            });
        }
    }
};
