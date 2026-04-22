<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use Database\Factories\IncidentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    /** @use HasFactory<IncidentFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'severity',
        'room_id',
        'status',
        'description',
        'equipment_reference',
        'attachment_path',
        'created_by',
        'owner_id',
        'follow_up_due_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'category' => IncidentCategory::class,
            'severity' => IncidentSeverity::class,
            'status' => IncidentStatus::class,
            'follow_up_due_at' => 'date',
            'resolved_at' => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(IncidentActivity::class);
    }
}
