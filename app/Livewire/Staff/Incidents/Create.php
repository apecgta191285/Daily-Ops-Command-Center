<?php

declare(strict_types=1);

namespace App\Livewire\Staff\Incidents;

use App\Application\Checklists\Data\ChecklistIncidentPrefill;
use App\Application\Incidents\Actions\CreateIncident;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentSubcategory;
use App\Models\Room;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public $title = '';

    public $category = '';

    public string $subcategory = '';

    public $severity = '';

    public $description = '';

    public string $roomId = '';

    public string $equipmentReference = '';

    public $attachment;

    public ?array $submissionRecap = null;

    public bool $prefilledFromChecklist = false;

    public array $categories = [];

    public array $subcategories = [];

    public array $severities = [];

    public array $rooms = [];

    public ?string $checklistReturnScope = null;

    public ?string $checklistReturnRoom = null;

    public function mount(): void
    {
        $this->categories = IncidentCategory::values();
        $this->subcategories = IncidentSubcategory::valuesForCategory(null);
        $this->severities = IncidentSeverity::values();
        $this->rooms = Room::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->map(fn (Room $room): array => [
                'id' => (string) $room->id,
                'name' => $room->name,
                'code' => $room->code,
            ])
            ->all();

        $prefill = ChecklistIncidentPrefill::fromRequest(
            request(),
            $this->categories,
            $this->severities,
        );

        if ($prefill !== null) {
            $this->prefilledFromChecklist = true;
            $this->title = $prefill->title;
            $this->category = $prefill->category ?? '';
            $this->refreshSubcategoryOptions();
            $this->subcategory = ($prefill->subcategory !== null && in_array($prefill->subcategory, $this->subcategories, true))
                ? $prefill->subcategory
                : '';
            $this->severity = $prefill->severity ?? '';
            $this->description = $prefill->description;
            $this->roomId = $prefill->roomId !== null ? (string) $prefill->roomId : '';
        }

        $scopeRouteKey = request()->string('checklist_scope')->value();
        $this->checklistReturnScope = ChecklistScope::fromRouteKey($scopeRouteKey)?->routeKey();
        $requestedRoomId = request()->integer('room');

        if ($this->roomId === '' && $requestedRoomId > 0) {
            $this->roomId = (string) $requestedRoomId;
        }

        if ($this->roomId !== '' && ! collect($this->rooms)->pluck('id')->contains($this->roomId)) {
            $this->roomId = '';
        }

        if ($this->roomId === '' && count($this->rooms) === 1) {
            $this->roomId = $this->rooms[0]['id'];
        }

        $this->checklistReturnRoom = $this->roomId !== '' ? $this->roomId : null;
    }

    public function submit(): void
    {
        if (count($this->rooms) === 0) {
            $this->addError('roomId', 'ยังไม่มีห้องที่เปิดใช้งานอยู่ในขณะนี้');

            return;
        }

        $this->validate([
            'title' => 'required|string|max:120',
            'category' => 'required|string|in:'.implode(',', $this->categories),
            'subcategory' => 'required|string|in:'.implode(',', IncidentSubcategory::valuesForCategory($this->category)),
            'severity' => 'required|string|in:'.implode(',', $this->severities),
            'roomId' => 'required|integer|exists:rooms,id',
            'description' => 'required|string',
            'equipmentReference' => 'nullable|string|max:120',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240', // Limit to 10MB
        ]);

        $incident = app(CreateIncident::class)([
            'title' => $this->title,
            'category' => $this->category,
            'subcategory' => $this->subcategory,
            'severity' => $this->severity,
            'room_id' => (int) $this->roomId,
            'description' => $this->description,
            'equipment_reference' => $this->equipmentReference,
        ], auth()->id(), $this->attachment);

        $this->submissionRecap = [
            'title' => $incident->title,
            'category' => $incident->category->value,
            'subcategory' => $incident->subcategory,
            'severity' => $incident->severity->value,
            'room_name' => $incident->room?->name ?? 'ไม่พบข้อมูลห้อง',
            'equipment_reference' => $incident->equipment_reference,
            'status' => $incident->status->value,
            'created_at' => $incident->created_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i'),
            'has_attachment' => $incident->attachment_path !== null,
            'from_checklist' => $this->prefilledFromChecklist,
        ];

        $this->reset(['title', 'category', 'subcategory', 'severity', 'description', 'equipmentReference', 'attachment']);
        $this->subcategories = IncidentSubcategory::valuesForCategory(null);
    }

    public function startAnother(): void
    {
        $this->submissionRecap = null;

        if ($this->prefilledFromChecklist) {
            $this->prefilledFromChecklist = false;
        }
    }

    /**
     * @return array<string, string>
     */
    public function checklistReturnParameters(): array
    {
        return array_filter([
            'scope' => $this->checklistReturnScope,
            'room' => $this->roomId !== '' ? $this->roomId : $this->checklistReturnRoom,
        ], static fn (?string $value) => $value !== null && $value !== '');
    }

    public function updatedCategory(): void
    {
        $this->refreshSubcategoryOptions();
    }

    private function refreshSubcategoryOptions(): void
    {
        $this->subcategories = IncidentSubcategory::valuesForCategory($this->category !== '' ? $this->category : null);

        if ($this->subcategory !== '' && ! in_array($this->subcategory, $this->subcategories, true)) {
            $this->subcategory = '';
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.staff.incidents.create');
    }
}
