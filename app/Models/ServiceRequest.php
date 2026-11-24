<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ServiceRequest extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ticket_number',
        'service_type_id',
        'nik',
        'kk_number',
        'full_name',
        'phone_number',
        'address',
        'purpose_description',
        'nik_blind_index',
        'status',
        'letter_number',
        'letter_generated_at',
        'admin_notes',
        'processed_by',
        'processed_at',
    ];

    /**
     * Kolom-kolom sensitif warga dienkripsi secara otomatis menggunakan AES-256 (At-Rest).
     * Di tabel database tersimpan sebagai ciphertext acak.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nik' => 'encrypted',
            'kk_number' => 'encrypted',
            'full_name' => 'encrypted',
            'phone_number' => 'encrypted',
            'address' => 'encrypted',
            'purpose_description' => 'encrypted',
            'letter_generated_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke master jenis layanan surat.
     */
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    /**
     * Relasi ke file lampiran pendukung privat (KTP/KK).
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(RequestDocument::class, 'attachable');
    }

    /**
     * Relasi ke admin petugas yang memproses.
     */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
