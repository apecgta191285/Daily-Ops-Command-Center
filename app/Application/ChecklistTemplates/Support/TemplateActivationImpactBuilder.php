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
        ?ChecklistTemplate $currentLiveTemplate,
    ): array {
        if (! $isActive) {
            return [
                'title' => 'Draft mode',
                'description' => 'This template will stay inactive after saving. Use this when you want to prepare a revision without changing the live daily checklist yet.',
                'tone' => 'info',
            ];
        }

        if ($editingTemplate?->exists && $editingTemplate->is_active) {
            return [
                'title' => 'Live template stays in place',
                'description' => 'This template is already the live daily checklist. Saving keeps it active and updates the current production version directly.',
                'tone' => 'info',
            ];
        }

        if ($currentLiveTemplate) {
            $historyNote = $currentLiveTemplate->runs_count > 0
                ? " The current live template already has {$currentLiveTemplate->runs_count} recorded run(s)."
                : '';

            return [
                'title' => 'Activation will retire the current live template',
                'description' => "Saving this template as active will retire \"{$currentLiveTemplate->title}\" from live use and replace the daily checklist immediately.{$historyNote}",
                'tone' => 'warning',
            ];
        }

        return [
            'title' => 'This becomes the first live template',
            'description' => 'No other active template exists right now, so saving this template as active will make it the live daily checklist.',
            'tone' => 'info',
        ];
    }
}
