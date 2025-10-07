<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'excerpt')) {
                $table->text('excerpt')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('articles', 'body')) {
                $table->longText('body')->nullable()->after('excerpt');
            }
            if (!Schema::hasColumn('articles', 'author')) {
                $table->string('author')->nullable()->after('body');
            }
            if (!Schema::hasColumn('articles', 'author_id')) {
                $table->foreignId('author_id')->nullable()->constrained('authors')->nullOnDelete()->after('author');
            }
            if (!Schema::hasColumn('articles', 'category')) {
                $table->string('category', 100)->nullable()->after('author_id');
            }
            if (!Schema::hasColumn('articles', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->after('category');
            }
            if (!Schema::hasColumn('articles', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('category_id');
            }
            if (!Schema::hasColumn('articles', 'published_at')) {
                $table->dateTime('published_at')->nullable()->index()->after('is_featured');
            }
            if (!Schema::hasColumn('articles', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('published_at');
            }
            if (!Schema::hasColumn('articles', 'tags')) {
                $table->json('tags')->nullable()->after('thumbnail');
            }
            if (!Schema::hasColumn('articles', 'sources')) {
                $table->json('sources')->nullable()->after('tags');
            }
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // On ne supprime rien en down pour éviter toute perte accidentelle.
        });
    }
};
