<?php

use App\Enums\Company\CompanyType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('companies', function (Blueprint $table) {
			$table->engine = "InnoDB";
			$table->bigIncrements('id');
			$table->string('name')->unique();
			$table->string('logo')->nullable();
            $table->string('street_address', 500)->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('companies')->onDelete('cascade');
			$table->text('address')->nullable();
			$table->text('city')->nullable();
			$table->text('postal_code')->nullable();
			$table->decimal('rate_per_hour')->nullable();
			$table->enum('rate_per_hour_unit', ['USD', 'EURO', 'PKR', 'Pound', 'CAD'])->nullable();
            $table->string('type')->nullable()->default(CompanyType::PARENT->value);
			$table->text('invoice_prefix')->nullable();
			$table->text('invoice_serial')->nullable();

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
		Schema::dropIfExists('companies');
	}
}
