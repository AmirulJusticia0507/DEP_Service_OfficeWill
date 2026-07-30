<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_todo_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('course_todo_id');
            $table->text('response_content')->nullable();
            $table->integer('score')->nullable();
            $table->string('status', 20)->default('PENDING')->comment('PENDING, PASSED, FAILED');
            $table->timestamps();

            $table->foreign('enrollment_id')->references('id')->on('course_enrollments')->onDelete('cascade');
            $table->foreign('course_todo_id')->references('id')->on('course_todos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_todo_responses');
    }
};
