<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function (Blueprint $table) {
			$table->engine = "InnoDB";
			$table->bigIncrements('id');
			$table->string('first_name');
			$table->string('last_name');
			$table->string('email')->unique();
			$table->string('alternative_email')->nullable();
			$table->timestamp('email_verified_at')->nullable();
			$table->string('password');
			$table->enum('account_type', ['active', 'de-active'])->nullable();
			$table->string('designation')->default('Software Engineer');
			$table->rememberToken();
			$table->string('phone')->nullable();
			$table->string('alternative_number')->nullable();
			$table->text('address')->nullable();
			$table->decimal('salary')->nullable();
			$table->unsignedBigInteger('company_id');
            $table->boolean('is_active')->default(false);
			$table->foreign('company_id')->references('id')->on('companies')
				->onDelete('restrict');
			$table->string('avatar')->nullable();
			$table->string('device_token')->nullable();
			$table->string('currency')->nullable();
			$table->softDeletes();
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
		Schema::dropIfExists('users');
	}
}
