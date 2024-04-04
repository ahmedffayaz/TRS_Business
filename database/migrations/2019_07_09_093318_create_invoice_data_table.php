<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceDataTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoice_data', function (Blueprint $table) {
			$table->id('id');
			$table->unsignedBigInteger('invoice_id');
			$table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('restrict');

			$table->unsignedBigInteger('task_id')->nullable();
			$table->foreign('task_id')->references('id')->on('tasks')->onDelete('restrict');

			$table->integer('time');
			$table->decimal('rate_per_hour')->nullable();
			$table->decimal('amount')->nullable();
            $table->text('comments');
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
		Schema::dropIfExists('invoice_data');
	}
}
