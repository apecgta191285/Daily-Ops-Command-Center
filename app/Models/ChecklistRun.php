<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistRun extends Model
{
    protected $fillable = [
        'checklist_template_id',
        'run_date',
        'assigned_team_or_scope',
        'created_by',
        'submitted_at',
        'submitted_by',
    ];

    protected function casts(): array
    {
        return [
            'run_date' => 'date',
            'submitted_at' => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────

    public function template(): BelongsTo
    {
        return $this->belongsTo(ChecklistTemplate::class, 'checklist_template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChecklistRunItem::class);
    }
}
