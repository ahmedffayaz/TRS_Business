<?php

use App\Enums\Project\ProjectType;
use App\Enums\Project\ProjectNature;
use App\Enums\Project\ProjectStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Enums\Project\ProjectIsAutoArchived;
use Illuminate\Database\Migrations\Migration;

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

			$table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('restrict');

            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('restrict');

			$table->string('name')->nullable();
			$table->date('start_date')->nullable();
			$table->date('end_date')->nullable();
			$table->string('status')->default(ProjectStatus::PENDING->value);
			$table->mediumText('description')->nullable();
			$table->decimal('budget')->nullable();
			$table->string('currency')->nullable();
            $table->decimal('hourly_rate')->nullable();
            $table->string('rate_unit')->nullable();
            $table->string('type')->default(ProjectType::FIXED->value);
            $table->boolean('is_auto_archived')->default(ProjectIsAutoArchived::ARCHIVE->value);
            $table->string('nature')->default(ProjectNature::FIXED->value);

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
