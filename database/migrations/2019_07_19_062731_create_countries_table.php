<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->bigIncrements('id');
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
