<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistTemplate extends Model
{
    protected $fillable = [
        'title',
        'description',
        'scope',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ── Relationships ────────────────────────────────────

    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(ChecklistRun::class);
    }
}
