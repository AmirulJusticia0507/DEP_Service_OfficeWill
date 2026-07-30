<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliations', function (Blueprint $table) {
            $table->string('parent_affiliation_code', 20)->nullable()->after('affiliation_name');
        });
    }

    public function down(): void
    {
        Schema::table('affiliations', function (Blueprint $table) {
            $table->dropColumn('parent_affiliation_code');
        });
    }
};
