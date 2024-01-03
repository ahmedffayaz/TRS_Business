<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoices', function (Blueprint $table) {
			$table->engine = "InnoDB";
			$table->bigIncrements('id');
			$table->unsignedBigInteger('project_id');
			$table->string('invoice_number')->unique();
			$table->string('file')->nullable();
			$table->string('currency');
			$table->decimal('total')->nullable();
			$table->decimal('deduction')->nullable();
			$table->text('notes')->nullable();
			$table->date('due_at');
			$table->timestamp('billed_at')->nullable();
			$table->enum('status', ['pending', 'processing', 'processed','partially_paid', 'paid'])->default('pending');
            $table->addColumn('boolean', 'send_emails')->default(false);
			$table->decimal('paid_amount')->nullable();
			$table->timestamps();
		});

		Schema::table('invoices', function($table) {
		       $table->foreign('project_id')->references('id')->on('projects')->onDelete('restrict');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('invoices');
	}
}
