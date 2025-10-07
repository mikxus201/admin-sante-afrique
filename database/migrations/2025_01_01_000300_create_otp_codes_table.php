<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->string('identifier');          // email ou téléphone
            $table->string('code_hash');
            $table->timestamp('expires_at');
            $table->timestamps();
            $table->index('identifier');
        });
    }
    public function down(): void { Schema::dropIfExists('otp_codes'); }
};
