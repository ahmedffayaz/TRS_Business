<?php

use App\Enums\Comment\CommentIsBillable;
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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->text('description');
            $table->string('time', 10)->nullable();
            $table->string('type')->nullable();
            $table->string('to', 20)->nullable();
            $table->string('from', 20)->nullable();
            $table->date('dated')->nullable();
            $table->timestamp('invoiced_at')->nullable();
            $table->boolean('is_billable')->default(CommentIsBillable::BILLABLE->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
