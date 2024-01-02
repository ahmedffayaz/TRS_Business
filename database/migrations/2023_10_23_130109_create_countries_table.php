<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('official_name')->unique()->nullable();
            $table->string('continent_name')->nullable();
            $table->string('alpha_2_code', 3)->nullable();
            $table->string('alpha_3_code', 4)->nullable();
            $table->string('numeric_code', 4)->nullable();
            $table->string('country_code', 5)->nullable();
            $table->string('official_language')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
