<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tasks', function (Blueprint $table) {
			$table->engine = "InnoDB";
			$table->bigIncrements('id');
			$table->string('name')->nullable();
			$table->text('description')->nullable();

			$table->unsignedBigInteger('user_id')->nullable()->index();
			$table->unsignedBigInteger('project_id');
			$table->unsignedBigInteger('task_type_id')->nullable();

			$table->enum('priority', ['low', 'medium', 'high'])->default('medium');
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->timestamp('completed_at')->nullable();
			$table->timestamp('billed_at')->nullable();

			$table->foreign('project_id')->references('id')->on('projects')->onDelete('restrict');
			$table->foreign('task_type_id')->references('id')->on('task_types')->onDelete('restrict');

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
		Schema::dropIfExists('tasks');
	}
}
