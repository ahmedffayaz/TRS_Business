<?php

use App\Enums\Leave\LeaveIsWorking;
use App\Enums\Leave\LeaveStatus;
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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('reason')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->boolean('is_working')->default(LeaveIsWorking::WORKING->value);
            $table->string('status')->default(LeaveStatus::PENDING->value);
            $table->unsignedBigInteger('processed_by');
            $table->text('processing_reason')->nullable();
            $table->timestamps();
        });

        Schema::table('leaves', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
