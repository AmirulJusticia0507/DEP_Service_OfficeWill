<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_todos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('todo_type', 20)->comment('QUESTIONNAIRE, REPORT, TEST');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0);
            $table->integer('passing_score')->nullable();
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_todos');
    }
};
