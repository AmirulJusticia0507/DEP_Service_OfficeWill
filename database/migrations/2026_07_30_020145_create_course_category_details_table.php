<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_category_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('detail_code', 20);
            $table->string('detail_name', 100);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('course_categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_category_details');
    }
};
