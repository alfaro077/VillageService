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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 32)->unique()->index();
            $table->foreignId('service_type_id')->constrained('service_types')->cascadeOnDelete();

            // Kolom Sensitif Warga (Tersimpan sebagai Ciphertext AES-256 di MySQL)
            // Menggunakan text() dan longText() agar muat menampung ciphertext panjang
            $table->text('nik');
            $table->text('kk_number');
            $table->text('full_name');
            $table->text('phone_number');
            $table->text('address');
            $table->longText('purpose_description')->nullable();

            // Blind Index untuk pencarian aman tanpa dekripsi seluruh database
            $table->string('nik_blind_index', 64)->nullable()->index();

            // Metadata dan Status Proses
            $table->enum('status', ['pending', 'diproses', 'selesai', 'ditolak'])->default('pending')->index();
            $table->text('admin_notes')->nullable();
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
        Schema::dropIfExists('service_requests');
    }
};
