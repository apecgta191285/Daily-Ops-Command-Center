<?php

declare(strict_types=1);

namespace App\Application\ChecklistTemplates\Actions;

use App\Models\ChecklistTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DuplicateChecklistTemplate
{
    public function __invoke(ChecklistTemplate $source): ChecklistTemplate
    {
        return DB::transaction(function () use ($source): ChecklistTemplate {
            $duplicate = ChecklistTemplate::query()->create([
                'title' => $this->generateDuplicateTitle($source->title),
                'description' => $source->description,
                'scope' => $source->scope,
                'is_active' => false,
            ]);

            $source->items()
                ->orderBy('sort_order')
                ->get()
                ->each(fn ($item) => $duplicate->items()->create([
                    'title' => $item->title,
                    'description' => $item->description,
                    'sort_order' => $item->sort_order,
                    'is_required' => $item->is_required,
                ]));

            return $duplicate->fresh('items');
        });
    }

    private function generateDuplicateTitle(string $originalTitle): string
    {
        $sequence = 1;

        do {
            $suffix = $sequence === 1 ? ' (Copy)' : " (Copy {$sequence})";
            $candidate = Str::limit($originalTitle, 120 - mb_strlen($suffix), '');
            $candidate .= $suffix;
            $exists = ChecklistTemplate::query()->where('title', $candidate)->exists();
            $sequence++;
        } while ($exists);

        return $candidate;
    }
}
