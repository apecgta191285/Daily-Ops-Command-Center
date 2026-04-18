<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ChecklistRunItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistRunItem extends Model
{
    /** @use HasFactory<ChecklistRunItemFactory> */
    use HasFactory;

    protected $fillable = [
        'checklist_run_id',
        'checklist_item_id',
        'result',
        'note',
        'checked_by',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_at' => 'datetime',
        ];
    }

    // ── Relationships ────────────────────────────────────

    public function run(): BelongsTo
    {
        return $this->belongsTo(ChecklistRun::class, 'checklist_run_id');
    }

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class, 'checklist_item_id');
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
