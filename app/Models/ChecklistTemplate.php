<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Checklists\Enums\ChecklistScope;
use Database\Factories\ChecklistTemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistTemplate extends Model
{
    /** @use HasFactory<ChecklistTemplateFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'scope',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'scope' => ChecklistScope::class,
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
