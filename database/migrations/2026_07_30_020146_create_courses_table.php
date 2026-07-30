<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_detail_id');
            $table->string('course_name', 200);
            $table->text('description')->nullable();
            $table->boolean('has_retest')->default(false);
            $table->integer('passing_score')->default(70);
            $table->timestamps();

            $table->foreign('category_detail_id')->references('id')->on('course_category_details');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
