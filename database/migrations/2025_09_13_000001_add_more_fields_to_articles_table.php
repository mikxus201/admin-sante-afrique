<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'category')) {
                $table->string('category', 100)->nullable()->after('slug');
            }
            if (!Schema::hasColumn('articles', 'featured')) {
                $table->boolean('featured')->default(false)->after('category');
            }
            if (!Schema::hasColumn('articles', 'views')) {
                $table->unsignedInteger('views')->default(0)->after('featured');
            }
            // On suppose que "thumbnail", "excerpt", "published_at" existent déjà chez toi.
            // Sinon décommente :
            // if (!Schema::hasColumn('articles', 'thumbnail')) {
            //     $table->string('thumbnail')->nullable()->after('title');
            // }
            // if (!Schema::hasColumn('articles', 'excerpt')) {
            //     $table->text('excerpt')->nullable()->after('thumbnail');
            // }
            // if (!Schema::hasColumn('articles', 'published_at')) {
            //     $table->timestamp('published_at')->nullable()->after('excerpt');
            // }
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'views'))    $table->dropColumn('views');
            if (Schema::hasColumn('articles', 'featured')) $table->dropColumn('featured');
            if (Schema::hasColumn('articles', 'category')) $table->dropColumn('category');
        });
    }
};
