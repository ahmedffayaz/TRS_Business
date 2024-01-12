<?php

use App\Enums\Leave\LeaveIsWorking;
use App\Enums\Leave\LeaveStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaves', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->text('reason')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_working')->default(LeaveIsWorking::WORKING->value);
            $table->string('status')->default(LeaveStatus::PENDING->value);
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->text('processing_reason')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
            $table->foreign('processed_by')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leaves');
    }
}
