<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->string('slug')->unique();
            $t->text('bio')->nullable();
            $t->string('photo')->nullable();   // chemin storage/public/authors/...
            $t->boolean('active')->default(true);
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
