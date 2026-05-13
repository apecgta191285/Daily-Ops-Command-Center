<?php

declare(strict_types=1);

namespace App\Application\Reports\Queries;

use App\Application\Reports\Data\IncidentReportFilters;
use App\Domain\Incidents\Enums\IncidentSeverity;
use App\Domain\Incidents\Enums\IncidentStatus;
use App\Models\Incident;
use Illuminate\Database\Eloquent\Builder;

class BuildIncidentReport
{
    /**
     * @return array{
     *     filters: array{start_date:string,end_date:string,room_id:int|null,category:string,subcategory:string,status:string,severity:string},
     *     summary: array{total_count:int,unresolved_count:int,resolved_count:int,high_severity_count:int,rooms_impacted_count:int},
     *     status_rows: list<array{status:string,total_count:int}>,
     *     severity_rows: list<array{severity:string,total_count:int}>,
     *     category_rows: list<array{category:string,total_count:int,unresolved_count:int,resolved_count:int,high_severity_count:int}>,
     *     subcategory_rows: list<array{category:string,subcategory:string,total_count:int,unresolved_count:int,resolved_count:int,high_severity_count:int}>,
     *     room_rows: list<array{room_id:int|null,room_name:string,room_code:string|null,total_count:int,unresolved_count:int,resolved_count:int,high_severity_count:int}>,
     *     recent_incidents: list<array{id:int,title:string,created_at:string,room_name:string,category:string,subcategory:string|null,severity:string,status:string,url:string}>
     * }
     */
    public function __invoke(IncidentReportFilters $filters): array
    {
        $query = $this->query($filters);

        return [
            'filters' => [
                'start_date' => $filters->startDate->toDateString(),
                'end_date' => $filters->endDate->toDateString(),
                'room_id' => $filters->roomId,
                'category' => $filters->category,
                'subcategory' => $filters->normalizedSubcategory(),
                'status' => $filters->status,
                'severity' => $filters->severity,
            ],
            'summary' => $this->summary((clone $query)),
            'status_rows' => $this->statusRows((clone $query)),
            'severity_rows' => $this->severityRows((clone $query)),
            'category_rows' => $this->categoryRows((clone $query)),
            'subcategory_rows' => $this->subcategoryRows((clone $query)),
            'room_rows' => $this->roomRows((clone $query)),
            'recent_incidents' => $this->recentIncidents((clone $query)),
        ];
    }

    public function query(IncidentReportFilters $filters): Builder
    {
        $category = $filters->categoryEnum();
        $status = $filters->statusEnum();
        $severity = $filters->severityEnum();
        $subcategory = $filters->normalizedSubcategory();

        return Incident::query()
            ->whereBetween('incidents.created_at', [
                $filters->startDate->startOfDay()->toDateTimeString(),
                $filters->endDate->endOfDay()->toDateTimeString(),
            ])
            ->when($filters->roomId !== null, fn (Builder $query) => $query->where('incidents.room_id', $filters->roomId))
            ->when($category !== null, fn (Builder $query) => $query->where('incidents.category', $category->value))
            ->when($subcategory !== '', fn (Builder $query) => $query->where('incidents.subcategory', $subcategory))
            ->when($status !== null, fn (Builder $query) => $query->where('incidents.status', $status->value))
            ->when($severity !== null, fn (Builder $query) => $query->where('incidents.severity', $severity->value));
    }

    /**
     * @return array{total_count:int,unresolved_count:int,resolved_count:int,high_severity_count:int,rooms_impacted_count:int}
     */
    protected function summary(Builder $query): array
    {
        $row = $query
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN status != ? THEN 1 ELSE 0 END) as unresolved_count', [IncidentStatus::Resolved->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as resolved_count', [IncidentStatus::Resolved->value])
            ->selectRaw('SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) as high_severity_count', [IncidentSeverity::High->value])
            ->selectRaw('COUNT(DISTINCT room_id) as rooms_impacted_count')
            ->first();

        return [
            'total_count' => (int) ($row?->total_count ?? 0),
            'unresolved_count' => (int) ($row?->unresolved_count ?? 0),
            'resolved_count' => (int) ($row?->resolved_count ?? 0),
            'high_severity_count' => (int) ($row?->high_severity_count ?? 0),
            'rooms_impacted_count' => (int) ($row?->rooms_impacted_count ?? 0),
        ];
    }

    /**
     * @return list<array{status:string,total_count:int}>
     */
    protected function statusRows(Builder $query): array
    {
        return $query
            ->select('status')
            ->selectRaw('COUNT(*) as total_count')
            ->groupBy('status')
            ->orderByDesc('total_count')
            ->orderBy('status')
            ->get()
            ->map(fn (Incident $row): array => [
                'status' => (string) $row->status->value,
                'total_count' => (int) $row->total_count,
            ])
            ->all();
    }

    /**
     * @return list<array{severity:string,total_count:int}>
     */
    protected function severityRows(Builder $query): array
    {
        return $query
            ->select('severity')
            ->selectRaw('COUNT(*) as total_count')
            ->groupBy('severity')
            ->orderByDesc('total_count')
            ->orderBy('severity')
            ->get()
            ->map(fn (Incident $row): array => [
                'severity' => (string) $row->severity->value,
                'total_count' => (int) $row->total_count,
            ])
            ->all();
    }

    /**
     * @return list<array{category:string,total_count:int,unresolved_count:int,resolved_count:int,high_severity_count:int}>
     */
    protected function categoryRows(Builder $query): array
    {
        return $query
            ->select('category')
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN status != ? THEN 1 ELSE 0 END) as unresolved_count', [IncidentStatus::Resolved->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as resolved_count', [IncidentStatus::Resolved->value])
            ->selectRaw('SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) as high_severity_count', [IncidentSeverity::High->value])
            ->groupBy('category')
            ->orderByDesc('total_count')
            ->orderBy('category')
            ->get()
            ->map(fn (Incident $row): array => [
                'category' => (string) $row->category->value,
                'total_count' => (int) $row->total_count,
                'unresolved_count' => (int) $row->unresolved_count,
                'resolved_count' => (int) $row->resolved_count,
                'high_severity_count' => (int) $row->high_severity_count,
            ])
            ->all();
    }

    /**
     * @return list<array{category:string,subcategory:string,total_count:int,unresolved_count:int,resolved_count:int,high_severity_count:int}>
     */
    protected function subcategoryRows(Builder $query): array
    {
        return $query
            ->select(['category', 'subcategory'])
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN status != ? THEN 1 ELSE 0 END) as unresolved_count', [IncidentStatus::Resolved->value])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as resolved_count', [IncidentStatus::Resolved->value])
            ->selectRaw('SUM(CASE WHEN severity = ? THEN 1 ELSE 0 END) as high_severity_count', [IncidentSeverity::High->value])
            ->groupBy('category', 'subcategory')
            ->orderByDesc('total_count')
            ->orderBy('category')
            ->orderBy('subcategory')
            ->limit(12)
            ->get()
            ->map(fn (Incident $row): array => [
                'category' => (string) $row->category->value,
                'subcategory' => (string) ($row->subcategory ?? 'ยังไม่ระบุ'),
                'total_count' => (int) $row->total_count,
                'unresolved_count' => (int) $row->unresolved_count,
                'resolved_count' => (int) $row->resolved_count,
                'high_severity_count' => (int) $row->high_severity_count,
            ])
            ->all();
    }

    /**
     * @return list<array{room_id:int|null,room_name:string,room_code:string|null,total_count:int,unresolved_count:int,resolved_count:int,high_severity_count:int}>
     */
    protected function roomRows(Builder $query): array
    {
        return $query
            ->leftJoin('rooms', 'rooms.id', '=', 'incidents.room_id')
            ->selectRaw('incidents.room_id as room_id')
            ->selectRaw('rooms.name as room_name')
            ->selectRaw('rooms.code as room_code')
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw('SUM(CASE WHEN incidents.status != ? THEN 1 ELSE 0 END) as unresolved_count', [IncidentStatus::Resolved->value])
            ->selectRaw('SUM(CASE WHEN incidents.status = ? THEN 1 ELSE 0 END) as resolved_count', [IncidentStatus::Resolved->value])
            ->selectRaw('SUM(CASE WHEN incidents.severity = ? THEN 1 ELSE 0 END) as high_severity_count', [IncidentSeverity::High->value])
            ->groupBy('incidents.room_id', 'rooms.name', 'rooms.code')
            ->orderByDesc('total_count')
            ->orderBy('rooms.name')
            ->limit(10)
            ->get()
            ->map(fn (Incident $row): array => [
                'room_id' => $row->room_id !== null ? (int) $row->room_id : null,
                'room_name' => (string) ($row->room_name ?? 'ไม่พบข้อมูลห้อง'),
                'room_code' => $row->room_code !== null ? (string) $row->room_code : null,
                'total_count' => (int) $row->total_count,
                'unresolved_count' => (int) $row->unresolved_count,
                'resolved_count' => (int) $row->resolved_count,
                'high_severity_count' => (int) $row->high_severity_count,
            ])
            ->all();
    }

    /**
     * @return list<array{id:int,title:string,created_at:string,room_name:string,category:string,subcategory:string|null,severity:string,status:string,url:string}>
     */
    protected function recentIncidents(Builder $query): array
    {
        return $query
            ->with('room')
            ->orderByDesc('incidents.created_at')
            ->orderByDesc('incidents.id')
            ->limit(10)
            ->get()
            ->map(fn (Incident $incident): array => [
                'id' => $incident->id,
                'title' => $incident->title,
                'created_at' => $incident->created_at?->format('d/m/Y H:i') ?? '-',
                'room_name' => $incident->room?->name ?? 'ไม่พบข้อมูลห้อง',
                'category' => $incident->category->value,
                'subcategory' => $incident->subcategory,
                'severity' => $incident->severity->value,
                'status' => $incident->status->value,
                'url' => route('incidents.show', $incident),
            ])
            ->all();
    }
}
