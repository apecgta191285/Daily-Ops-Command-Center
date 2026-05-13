<?php

declare(strict_types=1);

namespace App\Http\Controllers\Management;

use App\Application\Reports\Queries\BuildIncidentReport;
use App\Application\Reports\Support\IncidentReportFilterNormalizer;
use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportIncidentReportController extends Controller
{
    public function __invoke(
        Request $request,
        IncidentReportFilterNormalizer $normalizer,
        BuildIncidentReport $buildIncidentReport,
    ): StreamedResponse {
        $filters = $normalizer->filters($request->query());
        $filename = sprintf(
            'incident-report-%s-to-%s.csv',
            $filters->startDate->toDateString(),
            $filters->endDate->toDateString(),
        );

        return response()->streamDownload(function () use ($buildIncidentReport, $filters): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Incident ID',
                'Created At',
                'Title',
                'Room',
                'Room Code',
                'Category',
                'Subcategory',
                'Severity',
                'Status',
                'Owner',
                'Follow Up Due',
                'Equipment Reference',
            ]);

            $buildIncidentReport
                ->query($filters)
                ->leftJoin('rooms', 'rooms.id', '=', 'incidents.room_id')
                ->leftJoin('users as owners', 'owners.id', '=', 'incidents.owner_id')
                ->select([
                    'incidents.id',
                    'incidents.created_at',
                    'incidents.title',
                    'incidents.category',
                    'incidents.subcategory',
                    'incidents.severity',
                    'incidents.status',
                    'incidents.follow_up_due_at',
                    'incidents.equipment_reference',
                ])
                ->selectRaw('rooms.name as export_room_name')
                ->selectRaw('rooms.code as export_room_code')
                ->selectRaw('owners.name as export_owner_name')
                ->orderByDesc('incidents.created_at')
                ->orderByDesc('incidents.id')
                ->cursor()
                ->each(function (Incident $incident) use ($handle): void {
                    fputcsv($handle, [
                        $incident->id,
                        $incident->created_at?->format('Y-m-d H:i:s') ?? '',
                        $incident->title,
                        $incident->export_room_name ?? '',
                        $incident->export_room_code ?? '',
                        $incident->category->value,
                        $incident->subcategory ?? '',
                        $incident->severity->value,
                        $incident->status->value,
                        $incident->export_owner_name ?? '',
                        $incident->follow_up_due_at?->format('Y-m-d') ?? '',
                        $incident->equipment_reference ?? '',
                    ]);
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }
}
