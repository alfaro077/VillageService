<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class RequestDocument extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'document_type',
        'original_name',
        'file_path',
        'file_mime',
        'file_size',
    ];

    /**
     * Relasi polimorfik ke ServiceRequest atau Complaint.
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}
