<?php

declare(strict_types=1);

namespace App\Livewire\Admin\ChecklistTemplates;

use App\Application\ChecklistTemplates\Actions\SaveChecklistTemplate;
use App\Application\ChecklistTemplates\Support\ChecklistTemplateItemEditor;
use App\Application\ChecklistTemplates\Support\TemplateActivationImpactBuilder;
use App\Domain\Checklists\Enums\ChecklistScope;
use App\Models\ChecklistTemplate;
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
        $this->loadCurrentLiveTemplateContext();

        if ($this->template) {
            $this->runCount = $this->template->runs()->count();
            $this->hasRunHistory = $this->runCount > 0;
            $this->title = $this->template->title;
            $this->description = $this->template->description ?? '';
            $this->scope = $this->template->scope;
            $this->is_active = $this->template->is_active;
            $this->items = $this->itemEditor()->fromTemplate($this->template);

            return;
        }

        $this->scope = $this->scopes[0];
        $this->addItem();
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
            ? 'Refine the template structure used by daily checklist execution. Duplicate historically used templates before making larger structural changes.'
            : 'Create the checklist template that staff will use in the operations workspace.';
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
            $this->currentLiveTemplate(),
        );
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.admin.checklist-templates.manage');
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
}
