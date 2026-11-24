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
        Schema::create('request_documents', function (Blueprint $table) {
            $table->id();
            $table->string('attachable_type', 255);
            $table->unsignedBigInteger('attachable_id');
            $table->string('document_type', 50); // ktp, kk, pendukung, bukti
            $table->string('original_name');
            $table->string('file_path', 500);
            $table->string('file_mime', 100);
            $table->unsignedInteger('file_size');
            $table->timestamps();

            $table->index(['attachable_type', 'attachable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_documents');
    }
};
