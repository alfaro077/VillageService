<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Complaint extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ticket_number',
        'reporter_name',
        'reporter_nik',
        'reporter_phone',
        'complaint_title',
        'complaint_content',
        'location',
        'category',
        'status',
        'admin_response',
        'processed_by',
        'processed_at',
    ];

    /**
     * Kolom-kolom isi pengaduan dan identitas pelapor dienkripsi AES-256 (At-Rest).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reporter_name' => 'encrypted',
            'reporter_nik' => 'encrypted',
            'reporter_phone' => 'encrypted',
            'complaint_title' => 'encrypted',
            'complaint_content' => 'encrypted',
            'location' => 'encrypted',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke file lampiran/bukti pengaduan.
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
