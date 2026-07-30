<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affiliations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->string('affiliation_code', 20);
            $table->string('affiliation_name', 150);
            $table->integer('display_order')->default(0);
            $table->tinyInteger('organization_type')->nullable()->comment('1: Main store, 2: FC store');
            $table->timestamps();

            $table->unique(['company_id', 'affiliation_code']);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliations');
    }
};
