<?php


// database/migrations/2025_01_01_000000_add_email_verified_at_to_users.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('name');
            }
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'email_verified_at')) $table->dropColumn('email_verified_at');
            if (Schema::hasColumn('users', 'phone')) $table->dropColumn('phone');
        });
    }
};
