<?php

// database/migrations/2025_01_01_000100_create_newsletters_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('newsletter_topics', function (Blueprint $t) {
      $t->id();
      $t->string('slug')->unique();
      $t->string('name');
      $t->boolean('is_active')->default(true);
      $t->timestamps();
    });

    Schema::create('newsletter_user', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->constrained()->cascadeOnDelete();
      $t->foreignId('topic_id')->constrained('newsletter_topics')->cascadeOnDelete();
      $t->boolean('subscribed')->default(true);
      $t->timestamp('unsubscribed_at')->nullable();
      $t->timestamps();
      $t->unique(['user_id','topic_id']);
    });
  }
  public function down(): void {
    Schema::dropIfExists('newsletter_user');
    Schema::dropIfExists('newsletter_topics');
  }
};
