<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->string('job_id', 20);
            $table->string('job_title', 100);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_jobs');
    }
};
