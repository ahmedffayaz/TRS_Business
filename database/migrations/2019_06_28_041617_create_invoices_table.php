<?php

use App\Enums\Invoice\InvoiceStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
			$table->id();

			$table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('restrict');

			$table->string('invoice_number')->unique();
			$table->string('file')->nullable();
			$table->string('currency');
			$table->decimal('total')->nullable();
			$table->decimal('deduction')->nullable();
			$table->text('notes')->nullable();
			$table->date('due_at');
			$table->timestamp('billed_at')->nullable();
			$table->string('status')->default(InvoiceStatus::PENDING->value);
            $table->boolean('send_emails')->default(false);
			$table->decimal('paid_amount')->nullable();
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
		Schema::dropIfExists('invoices');
	}
}
