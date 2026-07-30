<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('place_of_birth')->nullable()->after('phone_number');
            $table->date('date_of_birth')->nullable()->after('place_of_birth');
            $table->enum('gender', ['MALE', 'FEMALE'])->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('gender');
            $table->string('photo')->nullable()->after('address');
            $table->json('preferences')->nullable()->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['place_of_birth', 'date_of_birth', 'gender', 'address', 'photo', 'preferences']);
        });
    }
};
