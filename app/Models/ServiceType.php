<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'required_documents',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'required_documents' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke pengajuan layanan surat warga.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }
}
