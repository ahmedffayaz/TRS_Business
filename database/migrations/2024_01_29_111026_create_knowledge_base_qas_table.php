<?php

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
        Schema::create('knowledge_base_qas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('knowledge_base_topic_id');
            $table->foreign('knowledge_base_topic_id')->references('id')->on('knowledge_base_topics')->onDelete('cascade');

            $table->string('question')->unique();
            $table->string('slug')->unique();
            $table->longText('answer');
            $table->string('keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_qas');
    }
};
