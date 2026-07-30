<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_categories', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('display_order');
        });

        Schema::table('course_category_details', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('display_order');
        });
    }

    public function down(): void
    {
        Schema::table('course_categories', function (Blueprint $table) {
            $table->dropColumn('icon');
        });

        Schema::table('course_category_details', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
