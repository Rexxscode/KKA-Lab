<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('signature_path')->nullable()->after('avatar_path');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->foreignId('super_admin_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->foreignId('guru_id')->nullable()->after('super_admin_id')->constrained('users')->nullOnDelete();
            $table->string('super_admin_nama')->nullable()->after('guru_id');
            $table->string('guru_nama')->nullable()->after('super_admin_nama');
            $table->string('super_admin_sign_path')->nullable()->after('guru_nama');
            $table->string('guru_sign_path')->nullable()->after('super_admin_sign_path');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('super_admin_id');
            $table->dropConstrainedForeignId('guru_id');
            $table->dropColumn(['super_admin_nama', 'guru_nama', 'super_admin_sign_path', 'guru_sign_path']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('signature_path');
        });
    }
};
