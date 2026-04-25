<?php

declare(strict_types=1);

namespace App\Livewire\Admin\ChecklistTemplates;

use App\Application\ChecklistTemplates\Actions\SaveChecklistTemplate;
use App\Application\ChecklistTemplates\Support\ChecklistTemplateItemEditor;
use App\Application\ChecklistTemplates\Support\TemplateActivationImpactBuilder;
use App\Application\ChecklistTemplates\Support\TemplateScopeGovernanceBuilder;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\ChecklistTemplate;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Manage extends Component
{
    public ?ChecklistTemplate $template = null;

    public bool $hasRunHistory = false;

    public int $runCount = 0;

    public string $title = '';

    public string $description = '';

    public string $scope = '';

    public bool $is_active = true;

    public array $items = [];

    public array $scopes = [];

    public ?string $currentLiveTemplateTitle = null;

    public int $currentLiveTemplateRunCount = 0;

    public function mount(?ChecklistTemplate $template = null): void
    {
        $this->scopes = ChecklistScope::values();
        $this->template = $template;

        if ($this->template) {
            $this->runCount = $this->template->runs()->count();
            $this->hasRunHistory = $this->runCount > 0;
            $this->title = $this->template->title;
            $this->description = $this->template->description ?? '';
            $this->scope = $this->template->scope->value;
            $this->is_active = $this->template->is_active;
            $this->items = $this->itemEditor()->fromTemplate($this->template);
            $this->loadCurrentLiveTemplateContext();

            return;
        }

        $this->scope = $this->scopes[0];
        $this->addItem();
        $this->loadCurrentLiveTemplateContext();
    }

    public function addItem(): void
    {
        $this->items = $this->itemEditor()->add($this->items);
    }

    public function removeItem(int $index): void
    {
        $this->items = $this->itemEditor()->remove($this->items, $index);
    }

    public function save(SaveChecklistTemplate $saveChecklistTemplate): void
    {
        $validated = $this->validate([
            'title' => [
                'required',
                'string',
                'max:120',
                Rule::unique('checklist_templates', 'title')->ignore($this->template?->getKey()),
            ],
            'description' => ['nullable', 'string'],
            'scope' => ['required', Rule::in($this->scopes)],
            'is_active' => ['boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.group_label' => ['nullable', 'string', 'max:80'],
            'items.*.sort_order' => ['required', 'integer', 'min:1'],
            'items.*.is_required' => ['boolean'],
        ]);

        $template = $saveChecklistTemplate($this->template, $validated);

        session()->flash('message', $this->template
            ? 'อัปเดตแม่แบบรายการตรวจเรียบร้อยแล้ว'
            : 'สร้างแม่แบบรายการตรวจเรียบร้อยแล้ว');

        $this->redirectRoute('templates.edit', $template, navigate: true);
    }

    public function getPageTitleProperty(): string
    {
        return $this->template ? 'แก้ไขแม่แบบรายการตรวจ' : 'สร้างแม่แบบรายการตรวจ';
    }

    public function getPageDescriptionProperty(): string
    {
        return $this->template
            ? 'ปรับปรุงแม่แบบรายการตรวจที่ผู้ตรวจห้องใช้อยู่แล้วในงานประจำวัน และควรทำสำเนาแม่แบบที่มีประวัติการใช้งานก่อนแก้โครงสร้างใหญ่'
            : 'สร้างแม่แบบรายการตรวจที่ผู้ตรวจห้องจะใช้ในงานประจำวันของห้องคอม';
    }

    /**
     * @return array{
     *     title: string,
     *     description: string,
     *     tone: 'info'|'warning'
     * }
     */
    public function getActivationImpactProperty(): array
    {
        return $this->activationImpactBuilder()(
            $this->template,
            $this->is_active,
            $this->scope,
            $this->currentLiveTemplate(),
        );
    }

    /**
     * @return array{
     *     item_count: int,
     *     grouped_section_count: int,
     *     required_count: int,
     *     optional_count: int,
     *     blank_description_count: int
     * }
     */
    public function getTemplateSummaryProperty(): array
    {
        $items = $this->itemsCollection();
        $groupedSections = $items
            ->pluck('group_label')
            ->map(fn (mixed $label): string => trim((string) $label))
            ->filter()
            ->unique()
            ->count();
        $requiredCount = $items->where('is_required', true)->count();

        return [
            'item_count' => $items->count(),
            'grouped_section_count' => $groupedSections,
            'required_count' => $requiredCount,
            'optional_count' => $items->count() - $requiredCount,
            'blank_description_count' => $items
                ->filter(fn (array $item): bool => trim((string) ($item['description'] ?? '')) === '')
                ->count(),
        ];
    }

    /**
     * @return list<array{
     *     tone: 'info'|'warning'|'success',
     *     title: string,
     *     body: string
     * }>
     */
    public function getAuthoringSignalsProperty(): array
    {
        $summary = $this->templateSummary;
        $signals = [];

        if ($summary['item_count'] < 3) {
            $signals[] = [
                'tone' => 'info',
                'title' => 'ฉบับร่างยังมีรายการน้อยเกินไป',
                'body' => 'ควรเพิ่มรายการตรวจอีกเล็กน้อยก่อนเปิดใช้งาน เพื่อให้แม่แบบอ่านเป็นงานเปิดห้อง ระหว่างวัน หรือปิดห้องที่ครบถ้วนจริง',
            ];
        }

        if ($summary['item_count'] >= 6 && $summary['grouped_section_count'] === 0) {
            $signals[] = [
                'tone' => 'warning',
                'title' => 'ควรจัดกลุ่มเพื่อให้อ่านง่ายขึ้น',
                'body' => 'แม่แบบนี้ยาวพอที่จะได้ประโยชน์จากการแบ่งกลุ่มรายการ ใช้ชื่อกลุ่มสั้น ๆ เพื่อให้ผู้ตรวจห้องไล่อ่านเป็นช่วงได้ชัดเจน',
            ];
        }

        if ($summary['blank_description_count'] > 0) {
            $signals[] = [
                'tone' => 'info',
                'title' => 'ควรเติมคำอธิบายในจุดที่อาจตีความไม่ตรงกัน',
                'body' => "ยังมี {$summary['blank_description_count']} รายการที่มีเพียงชื่อรายการ คำอธิบายสั้น ๆ จะช่วยให้ผู้ตรวจห้องเข้าใจรายการที่ไม่คุ้นเคยได้ตรงกันมากขึ้น",
            ];
        }

        if ($this->hasRunHistory) {
            $signals[] = [
                'tone' => 'warning',
                'title' => 'ควรระวังผลกระทบต่อประวัติเดิม',
                'body' => "แม่แบบนี้มีประวัติรอบการตรวจเช็กแล้ว {$this->runCount} รอบ หากต้องแก้โครงสร้างใหญ่ควรทำสำเนาก่อน เพื่อให้ความหมายของประวัติเดิมยังอ่านได้ชัดเจน",
            ];
        }

        if ($this->is_active) {
            $signals[] = [
                'tone' => 'success',
                'title' => 'แม่แบบนี้ถูกเตรียมไว้สำหรับใช้งานจริง',
                'body' => 'ควรตรวจลำดับ ชื่อกลุ่ม และสถานะรายการบังคับให้เรียบร้อยก่อนบันทึก เพราะการบันทึกแบบใช้งานจริงจะมีผลกับรายการตรวจของรอบเวลานี้ทันที',
            ];
        }

        if ($signals === []) {
            $signals[] = [
                'tone' => 'success',
                'title' => 'โครงสร้างของฉบับร่างอยู่ในสภาพที่ดี',
                'body' => 'แม่แบบนี้มีโครงสร้างพอสำหรับตรวจทานอย่างเป็นระบบแล้ว ใช้ตัวอย่างการแสดงผลและส่วนผลกระทบจากการเปิดใช้งานเพื่อตรวจลำดับการอ่านก่อนบันทึก',
            ];
        }

        return $signals;
    }

    /**
     * @return list<array{
     *     label: string,
     *     items: list<array{
     *         title: string,
     *         is_required: bool
     *     }>
     * }>
     */
    public function getPreviewGroupsProperty(): array
    {
        return $this->itemsCollection()
            ->map(function (array $item): array {
                return [
                    'label' => trim((string) ($item['group_label'] ?? '')) ?: 'ลำดับรายการที่ยังไม่ระบุกลุ่ม',
                    'title' => trim((string) ($item['title'] ?? '')) ?: 'รายการตรวจที่ยังไม่ตั้งชื่อ',
                    'is_required' => (bool) ($item['is_required'] ?? false),
                    'sort_order' => (int) ($item['sort_order'] ?? 0),
                ];
            })
            ->groupBy('label')
            ->map(function (Collection $items, string $label): array {
                return [
                    'label' => $label,
                    'items' => $items
                        ->sortBy('sort_order')
                        ->map(fn (array $item): array => [
                            'title' => $item['title'],
                            'is_required' => $item['is_required'],
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return list<array{
     *     scope: string,
     *     scope_key: string,
     *     live_template_title: ?string,
     *     live_template_id: ?int,
     *     draft_count: int,
     *     template_count: int,
     *     live_run_count: int,
     *     state: 'covered'|'missing',
     *     is_selected_scope: bool
     * }>
     */
    public function getScopeGovernanceProperty(): array
    {
        return collect(app(TemplateScopeGovernanceBuilder::class)())
            ->map(function (array $lane): array {
                $lane['is_selected_scope'] = $lane['scope'] === $this->scope;

                return $lane;
            })
            ->all();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.checklist-templates.manage');
    }

    public function updatedScope(): void
    {
        $this->loadCurrentLiveTemplateContext();
    }

    private function loadCurrentLiveTemplateContext(): void
    {
        $currentLiveTemplate = $this->currentLiveTemplate();

        $this->currentLiveTemplateTitle = $currentLiveTemplate?->title;
        $this->currentLiveTemplateRunCount = $currentLiveTemplate?->runs_count ?? 0;
    }

    private function currentLiveTemplate(): ?ChecklistTemplate
    {
        return ChecklistTemplate::query()
            ->withCount('runs')
            ->where('scope', $this->scope)
            ->where('is_active', true)
            ->when(
                $this->template?->exists,
                fn ($query) => $query->whereKeyNot($this->template->getKey()),
            )
            ->first();
    }

    private function itemEditor(): ChecklistTemplateItemEditor
    {
        return app(ChecklistTemplateItemEditor::class);
    }

    private function activationImpactBuilder(): TemplateActivationImpactBuilder
    {
        return app(TemplateActivationImpactBuilder::class);
    }

    private function itemsCollection(): Collection
    {
        return collect($this->items);
    }
}
