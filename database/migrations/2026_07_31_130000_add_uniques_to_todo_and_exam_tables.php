<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_todo_responses', function (Blueprint $table) {
            $table->unique(['enrollment_id', 'course_todo_id'], 'course_todo_responses_enrollment_todo_unique');
        });

        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->unique(['enrollment_id', 'course_todo_id', 'attempt_number'], 'exam_attempts_enrollment_todo_attempt_unique');
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->unique(['exam_attempt_id', 'question_id'], 'exam_answers_attempt_question_unique');
        });
    }

    public function down(): void
    {
        Schema::table('course_todo_responses', function (Blueprint $table) {
            $table->dropUnique('course_todo_responses_enrollment_todo_unique');
        });

        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropUnique('exam_attempts_enrollment_todo_attempt_unique');
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropUnique('exam_answers_attempt_question_unique');
        });
    }
};
