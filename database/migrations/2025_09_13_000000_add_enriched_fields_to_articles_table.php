<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // titre existe déjà

            if (!Schema::hasColumn('articles', 'slug')) {
                $table->string('slug')->nullable()->after('title');
                // Si tu veux l’unicité et que tu es sûr qu’elle n’existe pas déjà :
                // $table->unique('slug');
            }

            if (!Schema::hasColumn('articles', 'excerpt')) {
                $table->text('excerpt')->nullable()->after('content');
            }

            if (!Schema::hasColumn('articles', 'category')) {
                $table->string('category', 120)->nullable()->after('excerpt');
            }

            if (!Schema::hasColumn('articles', 'featured')) {
                $table->boolean('featured')->default(false)->after('category');
            }

            if (!Schema::hasColumn('articles', 'views')) {
                $table->unsignedInteger('views')->default(0)->after('featured');
            }

            if (!Schema::hasColumn('articles', 'author')) {
                $table->string('author')->nullable()->after('views');
            }

            if (!Schema::hasColumn('articles', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('author');
            }
        });

        // NOTE: ajouter un index unique après coup peut échouer si déjà présent.
        // Si tu veux vraiment garantir l’unicité du slug, assure-toi qu’aucun index
        // homonyme n’existe et ajoute ici un $table->unique('slug') dans un bloc
        // Schema::table séparé.
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Le dropColumn sur SQLite nécessite une version récente (>=3.35) ou doctrine/dbal.
            // On protège chaque suppression.
            if (Schema::hasColumn('articles', 'published_at')) {
                $table->dropColumn('published_at');
            }
            if (Schema::hasColumn('articles', 'author')) {
                $table->dropColumn('author');
            }
            if (Schema::hasColumn('articles', 'views')) {
                $table->dropColumn('views');
            }
            if (Schema::hasColumn('articles', 'featured')) {
                $table->dropColumn('featured');
            }
            if (Schema::hasColumn('articles', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('articles', 'excerpt')) {
                $table->dropColumn('excerpt');
            }
            if (Schema::hasColumn('articles', 'slug')) {
                // Si tu avais créé un index unique : $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }
};
