<?php

namespace App\Models;

use Database\Factories\IncidentActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentActivity extends Model
{
    /** @use HasFactory<IncidentActivityFactory> */
    use HasFactory;

    /**
     * Append-only log: no updated_at column.
     */
    public $timestamps = false;

    protected $fillable = [
        'incident_id',
        'action_type',
        'summary',
        'actor_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
