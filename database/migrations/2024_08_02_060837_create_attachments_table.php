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
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 16)->unique();
            $table->string('name');
            $table->string('tmpFilename');
            $table->text('file');
            $table->string('mimes');
            $table->string('size');
            $table->Integer('attachmentable_id');
            $table->string('attachmentable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachmentable_types');
    }
};
