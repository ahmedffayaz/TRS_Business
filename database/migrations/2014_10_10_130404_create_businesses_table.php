<?php

use App\Enums\Business\BusinessType;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id('id');
			$table->string('name')->unique();
            $table->string('slug')->unique();
			$table->string('logo')->nullable();
			$table->string('favicon')->nullable();
			$table->text('address');
			$table->text('city');
			$table->text('postal_code');

            $table->unsignedBigInteger('country_id');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');

			$table->text('invoice_prefix');
			$table->text('invoice_serial');

            $table->string('date_format')->nullable();
			$table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
