<?php

declare(strict_types=1);

namespace App\Livewire\Management\Notifications;

use App\Application\Notifications\Support\LineNotificationRedelivery;
use App\Domain\Access\Enums\UserRole;
use App\Models\NotificationDelivery;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class DeliveryIndex extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $eventType = '';

    #[Url(except: '')]
    public string $startDate = '';

    #[Url(except: '')]
    public string $endDate = '';

    /** @var list<string> */
    public array $statuses = [
        'sent',
        'failed',
        'failed_exception',
        'skipped_disabled',
        'skipped_incomplete_config',
    ];

    /** @var list<string> */
    public array $eventTypes = [
        'manual_test',
        'manual_redelivery',
        'incident_created',
        'incident_status_changed',
        'incident_accountability_changed',
    ];

    protected string $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $today = CarbonImmutable::today();

        $this->startDate = $this->validDateOrDefault($this->startDate, $today->subDays(6)->toDateString());
        $this->endDate = $this->validDateOrDefault($this->endDate, $today->toDateString());

        if (! in_array($this->status, $this->statuses, true)) {
            $this->status = '';
        }

        if (! in_array($this->eventType, $this->eventTypes, true)) {
            $this->eventType = '';
        }
    }

    public function applyPreset(string $preset): void
    {
        $today = CarbonImmutable::today();

        match ($preset) {
            '24h' => [$this->startDate, $this->endDate] = [$today->toDateString(), $today->toDateString()],
            '7d' => [$this->startDate, $this->endDate] = [$today->subDays(6)->toDateString(), $today->toDateString()],
            '30d' => [$this->startDate, $this->endDate] = [$today->subDays(29)->toDateString(), $today->toDateString()],
            default => null,
        };

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $today = CarbonImmutable::today();

        $this->status = '';
        $this->eventType = '';
        $this->startDate = $today->subDays(6)->toDateString();
        $this->endDate = $today->toDateString();
        $this->resetPage();
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['status', 'eventType', 'startDate', 'endDate'], true)) {
            $this->mount();
            $this->resetPage();
        }
    }

    public function redeliver(int $deliveryId): void
    {
        abort_unless(
            Auth::user() !== null && in_array(Auth::user()->role->value, UserRole::managementValues(), true),
            403,
        );

        $delivery = NotificationDelivery::query()
            ->with(['incident.room'])
            ->findOrFail($deliveryId);

        $redelivery = app(LineNotificationRedelivery::class);

        if (! $redelivery->canRedeliver($delivery)) {
            session()->flash('notification_delivery_error', 'รายการนี้ไม่สามารถส่งซ้ำได้ เพราะส่งสำเร็จแล้ว ไม่มี incident อ้างอิง หรือไม่ใช่ event ที่รองรับ');

            return;
        }

        $result = $redelivery($delivery);

        if ($result['status'] === 'sent') {
            session()->flash('notification_delivery_status', 'ส่งซ้ำไปยัง LINE สำเร็จ และบันทึก audit log ใหม่แล้ว');

            return;
        }

        session()->flash('notification_delivery_error', 'ส่งซ้ำไม่สำเร็จ: '.$result['message']);
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'sent' => 'ส่งสำเร็จ',
            'failed' => 'ส่งไม่สำเร็จ',
            'failed_exception' => 'ระบบส่งผิดพลาด',
            'skipped_disabled' => 'ปิดการแจ้งเตือน',
            'skipped_incomplete_config' => 'ตั้งค่าไม่ครบ',
            default => $status,
        };
    }

    public function statusChipClass(string $status): string
    {
        return match ($status) {
            'sent' => 'ops-chip--success',
            'failed', 'failed_exception' => 'ops-chip--danger',
            'skipped_incomplete_config' => 'ops-chip--warning',
            default => 'ops-chip--neutral',
        };
    }

    public function eventLabel(string $eventType): string
    {
        return match ($eventType) {
            'manual_test' => 'ทดสอบ LINE notification',
            'manual_redelivery' => 'ส่งซ้ำจาก audit log',
            'incident_created' => 'รายงานปัญหาใหม่',
            'incident_status_changed' => 'เปลี่ยนสถานะปัญหา',
            'incident_accountability_changed' => 'เปลี่ยนผู้รับผิดชอบ',
            default => $eventType,
        };
    }

    public function getDateRangeLabelProperty(): string
    {
        return CarbonImmutable::parse($this->startDate)->format('d/m/Y')
            .' - '.
            CarbonImmutable::parse($this->endDate)->format('d/m/Y');
    }

    /**
     * @return array{total:int,sent:int,failed:int,skipped:int}
     */
    public function summary(): array
    {
        $rows = $this->baseQuery()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $failed = (int) ($rows['failed'] ?? 0) + (int) ($rows['failed_exception'] ?? 0);
        $skipped = (int) ($rows['skipped_disabled'] ?? 0) + (int) ($rows['skipped_incomplete_config'] ?? 0);

        return [
            'total' => (int) $rows->sum(),
            'sent' => (int) ($rows['sent'] ?? 0),
            'failed' => $failed,
            'skipped' => $skipped,
        ];
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.management.notifications.delivery-index', [
            'deliveries' => $this->deliveries(),
            'summary' => $this->summary(),
        ]);
    }

    protected function deliveries(): LengthAwarePaginator
    {
        return $this->baseQuery()
            ->with(['incident.room'])
            ->latest('attempted_at')
            ->latest('id')
            ->paginate(15);
    }

    /**
     * @return Builder<NotificationDelivery>
     */
    protected function baseQuery(): Builder
    {
        $start = CarbonImmutable::parse($this->startDate)->startOfDay();
        $end = CarbonImmutable::parse($this->endDate)->endOfDay();

        return NotificationDelivery::query()
            ->whereBetween('attempted_at', [$start, $end])
            ->when($this->status !== '', fn (Builder $query): Builder => $query->where('status', $this->status))
            ->when($this->eventType !== '', fn (Builder $query): Builder => $query->where('event_type', $this->eventType));
    }

    protected function validDateOrDefault(string $date, string $default): string
    {
        if ($date === '') {
            return $default;
        }

        try {
            return CarbonImmutable::parse($date)->toDateString();
        } catch (\Throwable) {
            return $default;
        }
    }
}
