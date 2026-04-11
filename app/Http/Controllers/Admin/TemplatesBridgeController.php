<?php

namespace App\Http\Controllers\Admin;

use App\Filament\Resources\ChecklistTemplates\ChecklistTemplateResource;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class TemplatesBridgeController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.templates-bridge', [
            'adminTemplatesUrl' => ChecklistTemplateResource::getUrl('index', panel: 'admin'),
        ]);
    }
}
