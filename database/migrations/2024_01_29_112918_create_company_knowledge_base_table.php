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
        Schema::create('company_knowledge_base', function (Blueprint $table) {
            $table->id();

            // Define foreign keys for companies
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // Define foreign keys for knowledge base
            $table->unsignedBigInteger('knowledge_base_id');
            $table->foreign('knowledge_base_id')->references('id')->on('knowledge_bases')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_knowledge_base');
    }
};
