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
        Schema::create('knowledge_base_category_role', function (Blueprint $table) {
            $table->unsignedBigInteger('knowledge_base_category_id');
            $table->foreign('knowledge_base_category_id')->references('id')
                ->on('knowledge_base_categories')->onDelete('cascade');
            $table->unsignedBigInteger('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_category_role');
    }
};
