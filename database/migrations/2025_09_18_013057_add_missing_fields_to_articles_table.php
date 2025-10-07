<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Ajoute les colonnes manquantes *si* elles n’existent pas déjà
        if (!Schema::hasColumn('articles', 'body')
            || !Schema::hasColumn('articles', 'excerpt')
            || !Schema::hasColumn('articles', 'category_id')
            || !Schema::hasColumn('articles', 'published_at')
            || !Schema::hasColumn('articles', 'is_featured')
            || !Schema::hasColumn('articles', 'cover')
            || !Schema::hasColumn('articles', 'author_id')
        ) {
            Schema::table('articles', function (Blueprint $table) {
                if (!Schema::hasColumn('articles', 'body')) {
                    $table->longText('body')->nullable();
                }
                if (!Schema::hasColumn('articles', 'excerpt')) {
                    $table->text('excerpt')->nullable();
                }
                if (!Schema::hasColumn('articles', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->index();
                }
                if (!Schema::hasColumn('articles', 'author_id')) {
                    $table->foreignId('author_id')->nullable()->index();
                }
                if (!Schema::hasColumn('articles', 'cover')) {
                    $table->string('cover')->nullable();
                }
                if (!Schema::hasColumn('articles', 'is_featured')) {
                    $table->boolean('is_featured')->default(false);
                }
                if (!Schema::hasColumn('articles', 'published_at')) {
                    $table->dateTime('published_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        // On retire seulement ce qu’on a pu ajouter, si présent
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'published_at')) {
                $table->dropColumn('published_at');
            }
            if (Schema::hasColumn('articles', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
            if (Schema::hasColumn('articles', 'cover')) {
                $table->dropColumn('cover');
            }
            if (Schema::hasColumn('articles', 'author_id')) {
                $table->dropColumn('author_id');
            }
            if (Schema::hasColumn('articles', 'category_id')) {
                $table->dropColumn('category_id');
            }
            if (Schema::hasColumn('articles', 'excerpt')) {
                $table->dropColumn('excerpt');
            }
            if (Schema::hasColumn('articles', 'body')) {
                $table->dropColumn('body');
            }
        });
    }
};
