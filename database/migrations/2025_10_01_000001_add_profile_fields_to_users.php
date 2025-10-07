<?php
// database/migrations/2025_01_01_000001_add_profile_fields_to_users.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $t) {
            if (!Schema::hasColumn('users', 'nom')) $t->string('nom')->nullable();
            if (!Schema::hasColumn('users', 'prenoms')) $t->string('prenoms')->nullable();
            if (!Schema::hasColumn('users', 'phone')) $t->string('phone')->nullable();
            if (!Schema::hasColumn('users', 'gender')) $t->string('gender', 10)->nullable();
            if (!Schema::hasColumn('users', 'country')) $t->string('country', 100)->nullable();
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['nom','prenoms','phone','gender','country']);
        });
    }
};
