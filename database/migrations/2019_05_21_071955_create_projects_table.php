<?php

use App\Enums\Project\ProjectIsAutoArchived;
use App\Enums\Project\ProjectNature;
use App\Enums\Project\ProjectType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('projects', function (Blueprint $table) {
			$table->engine = "InnoDB";
			$table->bigIncrements('id');
			$table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('client_id');
			$table->unsignedBigInteger('client_company_id');
			$table->string('name')->nullable();
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->enum('status', ['pending', 'in-progress', 'delivered'])->nullable();
			$table->mediumText('description')->nullable();
			$table->decimal('budget')->nullable();
			$table->string('currency')->nullable();
            $table->decimal('hourly_rate')->nullable();
            $table->string('rate_unit')->nullable();
            $table->string('type')->default(ProjectType::FIXED->value);
            $table->boolean('is_auto_archived')->default(ProjectIsAutoArchived::ARCHIVED->value);
            $table->string('nature')->default(ProjectNature::FIXED->value);
			$table->foreign('company_id')->references('id')->on('companies')
				->onDelete('restrict');
			$table->foreign('client_company_id')->references('id')->on('companies')
				->onDelete('restrict');

			$table->string('reports_schedule')->nullable();
			$table->timestamp('last_updated_at')->nullable();
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
		Schema::dropIfExists('projects');

	}
}
