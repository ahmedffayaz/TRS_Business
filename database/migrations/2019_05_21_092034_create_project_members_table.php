<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectMembersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('project_members', function (Blueprint $table) {
			$table->engine = "InnoDB";
			$table->bigIncrements('id');
			$table->unsignedBigInteger('user_id');
			$table->unsignedBigInteger('project_id');
			$table->timestamp('assigned_at');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
			$table->foreign('project_id')->references('id')->on('projects')->onDelete('restrict');
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
		Schema::dropIfExists('project_members');
	}
}
