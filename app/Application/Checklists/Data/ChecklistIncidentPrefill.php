<?php

declare(strict_types=1);

namespace App\Application\Checklists\Data;

use App\Domain\Incidents\Enums\IncidentSubcategory;
use Illuminate\Http\Request;

readonly class ChecklistIncidentPrefill
{
    public function __construct(
        public string $title,
        public ?string $category,
        public ?string $subcategory,
        public ?string $severity,
        public string $description,
        public ?int $roomId = null,
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
        $subcategory = $request->string('subcategory')->value();
        $severity = $request->string('severity')->value();
        $description = $request->string('description')->trim()->value();
        $roomId = $request->integer('room');

        $normalizedCategory = in_array($category, $validCategories, true) ? $category : null;
        $normalizedSubcategory = $normalizedCategory !== null
            && IncidentSubcategory::isValidForCategory($subcategory, $normalizedCategory)
                ? $subcategory
                : null;

        return new self(
            title: $title,
            category: $normalizedCategory,
            subcategory: $normalizedSubcategory,
            severity: in_array($severity, $validSeverities, true) ? $severity : null,
            description: $description,
            roomId: $roomId > 0 ? $roomId : null,
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
            'subcategory' => $this->subcategory,
            'severity' => $this->severity,
            'description' => $this->description,
            'room' => $this->roomId !== null ? (string) $this->roomId : null,
        ], static fn (?string $value) => $value !== null && $value !== '');
    }
}
