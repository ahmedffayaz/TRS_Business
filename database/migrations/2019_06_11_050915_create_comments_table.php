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
			$table->engine = "InnoDB";
			$table->bigIncrements('id');
			$table->text('description');
			$table->unsignedInteger('time')->nullable();
			$table->enum('type', ['assigned', 'removed', 'comment', 'time', 'attachment']);
            $table->enum('is_billable', ['1', '0'])->default('1');
			$table->unsignedBigInteger('task_id');
			$table->unsignedBigInteger('to')->nullable()->index();
			$table->unsignedBigInteger('from');
            $table->date('dated')->nullable();
			$table->timestamp('invoiced_at')->nullable();
			$table->foreign('task_id')->references('id')
				->on('tasks')->onDelete('restrict');
            $table->foreign('from')->references('id')->on('users')
                ->onDelete('restrict');
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
		Schema::dropIfExists('comments');
	}
}
