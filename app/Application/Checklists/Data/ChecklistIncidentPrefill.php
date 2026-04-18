<?php

declare(strict_types=1);

namespace App\Application\Checklists\Data;

use Illuminate\Http\Request;

readonly class ChecklistIncidentPrefill
{
    public function __construct(
        public string $title,
        public ?string $category,
        public ?string $severity,
        public string $description,
    ) {}

    /**
     * @param  list<string>  $validCategories
     * @param  list<string>  $validSeverities
     */
    public static function fromRequest(Request $request, array $validCategories, array $validSeverities): ?self
    {
        if ($request->string('from')->value() !== 'checklist') {
            return null;
        }

        $title = (string) $request->string('title')->trim()->limit(120);
        $category = $request->string('category')->value();
        $severity = $request->string('severity')->value();
        $description = $request->string('description')->trim()->value();

        return new self(
            title: $title,
            category: in_array($category, $validCategories, true) ? $category : null,
            severity: in_array($severity, $validSeverities, true) ? $severity : null,
            description: $description,
        );
    }

    /**
     * @return array<string, string>
     */
    public function toRouteParameters(): array
    {
        return array_filter([
            'from' => 'checklist',
            'title' => $this->title,
            'category' => $this->category,
            'severity' => $this->severity,
            'description' => $this->description,
        ], static fn (?string $value) => $value !== null && $value !== '');
    }
}
