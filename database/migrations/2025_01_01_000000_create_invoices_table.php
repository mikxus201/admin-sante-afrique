<?php

// database/migrations/2025_01_01_000000_create_invoices_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('invoices', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->constrained()->cascadeOnDelete();
      $t->string('number')->unique();
      $t->date('period_from')->nullable();
      $t->date('period_to')->nullable();
      $t->unsignedInteger('amount_fcfa')->default(0);
      $t->enum('status', ['paid','unpaid','refunded'])->default('unpaid');
      $t->string('pdf_path')->nullable(); // ex: invoices/INV-2025-0001.pdf
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('invoices'); }
};
