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
            ? 'Checklist template updated successfully.'
            : 'Checklist template created successfully.');

        $this->redirectRoute('templates.edit', $template, navigate: true);
    }

    public function getPageTitleProperty(): string
    {
        return $this->template ? 'Edit Checklist Template' : 'Create Checklist Template';
    }

    public function getPageDescriptionProperty(): string
    {
        return $this->template
            ? 'Refine the checklist that staff already use in this lab routine. Duplicate historically used templates before making larger structural changes.'
            : 'Create the checklist template that duty staff will use during the daily lab routine.';
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
                'title' => 'Early draft shape',
                'body' => 'Add a few more items before activation so the checklist reads like a complete opening, during-day, or closing routine instead of one isolated check.',
            ];
        }

        if ($summary['item_count'] >= 6 && $summary['grouped_section_count'] === 0) {
            $signals[] = [
                'tone' => 'warning',
                'title' => 'Grouping will improve scan speed',
                'body' => 'This draft is long enough to benefit from group labels. Use lightweight sections so duty staff can scan the checklist in clear phases instead of one uninterrupted list.',
            ];
        }

        if ($summary['blank_description_count'] > 0) {
            $signals[] = [
                'tone' => 'info',
                'title' => 'Add context where ambiguity matters',
                'body' => "{$summary['blank_description_count']} item(s) still rely on title alone. Short descriptions help staff interpret unusual checks without overloading every row.",
            ];
        }

        if ($this->hasRunHistory) {
            $signals[] = [
                'tone' => 'warning',
                'title' => 'Revision safety matters here',
                'body' => "This template already has {$this->runCount} recorded run(s). Duplicate it before major restructuring when you want the historical meaning of past runs to stay obvious.",
            ];
        }

        if ($this->is_active) {
            $signals[] = [
                'tone' => 'success',
                'title' => 'This draft is pointed at live use',
                'body' => 'Review ordering, section labels, and required flags carefully. Saving active changes the live checklist duty staff will use next for this scope.',
            ];
        }

        if ($signals === []) {
            $signals[] = [
                'tone' => 'success',
                'title' => 'Authoring baseline looks healthy',
                'body' => 'This draft already has enough structure to review calmly. Use the live preview and activation lane to confirm the staff-facing reading order before save.',
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
                    'label' => trim((string) ($item['group_label'] ?? '')) ?: 'Unlabelled sequence',
                    'title' => trim((string) ($item['title'] ?? '')) ?: 'Untitled checklist item',
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
