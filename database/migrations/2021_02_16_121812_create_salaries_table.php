<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('salary');
            $table->decimal('salary_per_hour');
            $table->timestamps();

            $table->foreign('project_id')->references('id')
                ->on('projects')->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salaries');
    }
}
