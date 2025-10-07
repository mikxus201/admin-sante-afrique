<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('help_items', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('subscribe'); // ex: subscribe
            $table->string('key')->unique();               // ex: faq, info
            $table->string('title');
            $table->text('content')->nullable();
            $table->boolean('is_published')->default(true);
            $table->integer('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('help_items');
    }
};
