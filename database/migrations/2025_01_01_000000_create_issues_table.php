<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasTable('issues')) {
            Schema::create('issues', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('number')->unique();
                $table->date('date')->nullable();
                $table->string('cover')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();
            });
        }
    }
    public function down(): void {
        Schema::dropIfExists('issues');
    }
};
