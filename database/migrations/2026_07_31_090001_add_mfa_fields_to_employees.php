<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('mfa_enabled')->default(false)->after('password_error_count');
            $table->string('mfa_otp_hash')->nullable()->after('mfa_enabled');
            $table->timestamp('mfa_otp_expires_at')->nullable()->after('mfa_otp_hash');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['mfa_enabled', 'mfa_otp_hash', 'mfa_otp_expires_at']);
        });
    }
};
