<?php

declare(strict_types=1);

namespace App\Http\Controllers\Management;

use App\Application\Checklists\Support\ChecklistRunArchiveRecapBuilder;
use App\Http\Controllers\Controller;
use App\Models\ChecklistRun;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class PrintChecklistRunRecapController extends Controller
{
    public function __invoke(ChecklistRun $run): View
    {
        abort_if($run->submitted_at === null, 404);

        $run->loadMissing([
            'template',
            'creator',
            'submitter',
            'items.checklistItem',
            'items.checker',
        ]);

        $recap = app(ChecklistRunArchiveRecapBuilder::class)($run);

        return view('management.checklists.print-recap', [
            'run' => $run,
            'recap' => $recap,
            'pageTitle' => Str::of($run->template?->title ?? 'Checklist run')
                ->append(' printable recap')
                ->toString(),
            'scopeLabel' => $run->assigned_team_or_scope ?: ($run->template?->scope ?? 'Unknown scope'),
            'submittedByLabel' => $run->submitter?->name ?? $run->creator?->name ?? 'Unknown',
        ]);
    }
}
