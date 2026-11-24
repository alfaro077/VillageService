<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;

class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'description',
        'subject_type',
        'subject_id',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke user petugas yang melakukan aktivitas.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi polimorfik ke subjek aktivitas.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Catat log aktivitas dengan praktis dari controller.
     */
    public static function log(
        ?Request $request,
        string $action,
        string $description,
        ?Model $subject = null,
        ?User $user = null
    ): self {
        $currentUser = $user ?? $request?->user();

        return self::create([
            'user_id' => $currentUser?->id,
            'user_name' => $currentUser?->name ?? 'Sistem / Anonim',
            'user_role' => $currentUser?->role ?? 'guest',
            'action' => strtoupper($action),
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => $request?->ip() ?? '127.0.0.1',
            'user_agent' => $request?->userAgent() ? substr($request->userAgent(), 0, 500) : null,
            'created_at' => now(),
        ]);
    }
}
