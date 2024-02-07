<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKnowledgeBaseRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('knowledge_base_role', function (Blueprint $table) {
            $table->unsignedBigInteger('knowledge_base_id');
            $table->foreign('knowledge_base_id')->references('id')->on('knowledge_bases')->onDelete('cascade');
            $table->unsignedBigInteger('role_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('knowledge_base_role');
    }
}
