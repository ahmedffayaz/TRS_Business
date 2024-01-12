<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoicePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id');
            $table->decimal('amount')->nullable();
            $table->decimal('conversion_rate')->nullable();
            $table->decimal('remaining_amount')->nullable();
            $table->string('bank')->nullable();
            $table->string('bank_charges')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('billed_at')->nullable();
            $table->timestamps();
            // $table->foreign('invoice_id')->references('id')->on('invoices');
        });

        Schema::table('invoice_payments', function($table) {
               $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_payments');
    }
}
