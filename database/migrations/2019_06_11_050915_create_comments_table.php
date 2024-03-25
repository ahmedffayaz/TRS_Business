<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comments', function (Blueprint $table) {
			$table->id();
			$table->text('description');

			$table->unsignedInteger('time')->nullable();

            $table->string('type');

            $table->unsignedBigInteger('task_id');
			$table->foreign('task_id')->references('id')->on('tasks')->onDelete('restrict');

			$table->unsignedBigInteger('to')->nullable()->index();

			$table->unsignedBigInteger('from');
            $table->foreign('from')->references('id')->on('users')->onDelete('restrict');

            $table->date('dated')->nullable();

            $table->boolean('is_billable')->default('1');

			$table->timestamp('invoiced_at')->nullable();
			$table->timestamps();
            $table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('comments');
	}
}
