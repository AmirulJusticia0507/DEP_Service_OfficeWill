<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->string('employee_code', 30);
            $table->string('full_name', 100);
            $table->string('kana_name', 100)->nullable();
            $table->string('email', 150);
            $table->string('phone_number', 30)->nullable();
            $table->string('password');
            $table->integer('password_error_count')->default(0);
            $table->string('account_status', 20)->default('ACTIVE')->comment('ACTIVE, LOCKED, INACTIVE');
            $table->dateTime('account_locked_at')->nullable();
            $table->boolean('is_sys_admin')->default(false);
            $table->boolean('can_register_employee')->default(false);
            $table->boolean('can_register_course')->default(false);
            $table->boolean('can_setting_attendance')->default(false);
            $table->string('authority_effective_range', 20)->default('ONLY')->comment('ONLY, BELOW, ALL');
            $table->string('authority_effective_affiliation_code', 20)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['company_id', 'employee_code']);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
