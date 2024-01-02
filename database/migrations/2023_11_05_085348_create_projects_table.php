<?php

use App\Enums\Project\ProjectIsAutoArchived;
use App\Enums\Project\ProjectNature;
use App\Enums\Project\ProjectType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('client_id');
            $table->string('name');
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('status')->nullable();
            $table->mediumText('description')->nullable();
            $table->decimal('budget')->nullable();
            $table->decimal('rate_per_hour')->nullable();
            $table->string('rate_unit')->nullable();
            $table->string('type')->default(ProjectType::FIXED->value);
            $table->boolean('is_auto_archived')->default(ProjectIsAutoArchived::ARCHIVED->value);
            $table->string('nature')->default(ProjectNature::FIXED->value);
            $table->string('reports_schedule')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
