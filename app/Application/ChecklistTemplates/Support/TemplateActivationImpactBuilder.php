<?php

declare(strict_types=1);

namespace App\Application\ChecklistTemplates\Support;

use App\Models\ChecklistTemplate;

class TemplateActivationImpactBuilder
{
    /**
     * @return array{
     *     title: string,
     *     description: string,
     *     tone: 'info'|'warning'
     * }
     */
    public function __invoke(
        ?ChecklistTemplate $editingTemplate,
        bool $isActive,
        string $scopeLabel,
        ?ChecklistTemplate $currentLiveTemplate,
    ): array {
        if (! $isActive) {
            return [
                'title' => 'Draft mode',
                'description' => "This template will stay inactive after saving. Use this when you want to prepare a {$scopeLabel} revision without changing the live runtime yet.",
                'tone' => 'info',
            ];
        }

        if ($editingTemplate?->exists && $editingTemplate->is_active) {
            return [
                'title' => 'Live scope template stays in place',
                'description' => "This template is already the live {$scopeLabel} checklist. Saving keeps it active and updates the current production version for that scope directly.",
                'tone' => 'info',
            ];
        }

        if ($currentLiveTemplate) {
            $historyNote = $currentLiveTemplate->runs_count > 0
                ? " The current live template already has {$currentLiveTemplate->runs_count} recorded run(s)."
                : '';

            return [
                'title' => 'Activation will retire the current live template for this scope',
                'description' => "Saving this template as active will retire \"{$currentLiveTemplate->title}\" from live {$scopeLabel} use and replace that scope immediately.{$historyNote}",
                'tone' => 'warning',
            ];
        }

        return [
            'title' => 'This becomes the first live template for this scope',
            'description' => "No other active {$scopeLabel} template exists right now, so saving this template as active will make it the live checklist for that scope.",
            'tone' => 'info',
        ];
    }
}
