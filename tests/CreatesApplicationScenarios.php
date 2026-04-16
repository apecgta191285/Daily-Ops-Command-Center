<?php

namespace Tests;

use App\Domain\Access\Enums\UserRole;
use App\Domain\Checklists\Enums\ChecklistResult;
use App\Domain\Incidents\Enums\IncidentCategory;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\ChecklistItem;
use App\Models\ChecklistRun;
use App\Models\ChecklistRunItem;
use App\Models\ChecklistTemplate;
use App\Models\Incident;
use App\Models\IncidentActivity;
use App\Models\User;

trait CreatesApplicationScenarios
{
    protected function createUserForRole(UserRole $role, array $attributes = []): User
    {
        $factory = User::factory();

        $factory = match ($role) {
            UserRole::Admin => $factory->admin(),
            UserRole::Supervisor => $factory->supervisor(),
            UserRole::Staff => $factory->staff(),
        };

        return $factory->create($attributes);
    }

    /**
     * @param  list<array{title:string,description:?string,group_label?:?string,is_required?:bool}>|null  $items
     */
    protected function createTemplateWithItems(array $attributes = [], ?array $items = null): ChecklistTemplate
    {
        $template = ChecklistTemplate::factory()->create($attributes);

        $items ??= [
            ['title' => 'Checklist item 1', 'description' => 'Default checklist item 1', 'is_required' => true],
            ['title' => 'Checklist item 2', 'description' => 'Default checklist item 2', 'is_required' => true],
        ];

        foreach (array_values($items) as $index => $item) {
            ChecklistItem::factory()->create([
                'checklist_template_id' => $template->id,
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'group_label' => $item['group_label'] ?? null,
                'sort_order' => $index + 1,
                'is_required' => $item['is_required'] ?? true,
            ]);
        }

        return $template->fresh('items');
    }

    /**
     * @param  list<array{result:?string,note:?string}>|null  $itemStates
     */
    protected function createRunForUser(
        User $user,
        ChecklistTemplate $template,
        bool $submitted = false,
        ?array $itemStates = null,
        ?string $runDate = null,
    ): ChecklistRun {
        $run = ChecklistRun::factory()->create([
            'checklist_template_id' => $template->id,
            'run_date' => $runDate ?? today(),
            'assigned_team_or_scope' => $template->scope,
            'created_by' => $user->id,
            'submitted_at' => $submitted ? now() : null,
            'submitted_by' => $submitted ? $user->id : null,
        ]);

        $itemStates ??= $template->items->map(fn () => [
            'result' => $submitted ? ChecklistResult::Done->value : null,
            'note' => null,
        ])->values()->all();

        foreach ($template->items->values() as $index => $item) {
            $state = $itemStates[$index] ?? ['result' => null, 'note' => null];

            ChecklistRunItem::factory()->create([
                'checklist_run_id' => $run->id,
                'checklist_item_id' => $item->id,
                'result' => $state['result'],
                'note' => $state['note'],
                'checked_by' => $submitted ? $user->id : null,
                'checked_at' => $submitted ? now() : null,
            ]);
        }

        return $run->fresh('items');
    }

    protected function createIncidentWithActivity(
        User $creator,
        array $attributes = [],
        ?User $statusActor = null,
    ): Incident {
        $incident = Incident::factory()->create(array_merge([
            'created_by' => $creator->id,
            'category' => IncidentCategory::ComputerEquipment->value,
            'severity' => IncidentSeverity::Medium->value,
            'status' => IncidentStatus::Open->value,
        ], $attributes));

        IncidentActivity::factory()->create([
            'incident_id' => $incident->id,
            'action_type' => 'created',
            'summary' => 'Incident reported',
            'actor_id' => $creator->id,
        ]);

        if ($incident->status !== IncidentStatus::Open->value) {
            IncidentActivity::factory()->create([
                'incident_id' => $incident->id,
                'action_type' => 'status_changed',
                'summary' => "Status updated to {$incident->status}",
                'actor_id' => ($statusActor ?? $creator)->id,
                'created_at' => now()->addMinutes(10),
            ]);
        }

        return $incident->fresh('activities');
    }
}
