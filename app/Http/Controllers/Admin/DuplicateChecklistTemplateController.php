<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Application\ChecklistTemplates\Actions\DuplicateChecklistTemplate;
use App\Models\ChecklistTemplate;
use Illuminate\Http\RedirectResponse;

class DuplicateChecklistTemplateController
{
    public function __invoke(
        ChecklistTemplate $template,
        DuplicateChecklistTemplate $duplicateChecklistTemplate,
    ): RedirectResponse {
        $duplicate = $duplicateChecklistTemplate($template);

        return redirect()
            ->route('templates.edit', $duplicate)
            ->with('message', 'Checklist template duplicated. Review the copy, then activate it when ready.');
    }
}
