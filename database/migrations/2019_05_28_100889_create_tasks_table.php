<?php

use App\Enums\Task\TaskPriority;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
			$table->id();
			$table->string('name');
			$table->text('description')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

			$table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('restrict');

			$table->unsignedBigInteger('task_type_id')->nullable();
			$table->foreign('task_type_id')->references('id')->on('task_types')->onDelete('restrict');

            $table->string('priority')->default(TaskPriority::MEDIUM->value);
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->timestamp('completed_at')->nullable();
			$table->timestamp('billed_at')->nullable();

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
		Schema::dropIfExists('tasks');
	}
}
