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
			$table->engine = "InnoDB";
			$table->bigIncrements('id');
			$table->unsignedBigInteger('invoice_id');
			$table->unsignedBigInteger('task_id');
			$table->integer('time');
			$table->decimal('rate_per_hour')->nullable();
			$table->decimal('amount')->nullable();
			// $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('restrict');
			// $table->foreign('task_id')->references('id')->on('tasks')->onDelete('restrict');
			$table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('restrict');
			$table->foreign('task_id')->references('id')->on('tasks')->onDelete('restrict');
			
            $table->addColumn('text', 'comments');
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
