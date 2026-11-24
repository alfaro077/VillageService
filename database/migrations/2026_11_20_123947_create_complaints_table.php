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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 32)->unique()->index();

            // Kolom Sensitif Pengaduan (Tersimpan sebagai Ciphertext AES-256 di MySQL)
            // Menggunakan text() dan longText() untuk memastikan kapasitas ciphertext
            $table->text('reporter_name');
            $table->text('reporter_nik')->nullable();
            $table->text('reporter_phone')->nullable();
            $table->text('complaint_title');
            $table->longText('complaint_content');
            $table->text('location')->nullable();

            // Kategori dan Status Publik
            $table->string('category', 100)->default('Umum');
            $table->enum('status', ['menunggu', 'ditindaklanjuti', 'selesai', 'ditolak'])->default('menunggu')->index();
            $table->text('admin_response')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
