<?php

namespace App\Livewire\Admin\AuditLogs;

use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Audit & Log Aktivitas - Panel Admin')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'actor', history: true)]
    public string $actorFilter = 'all';

    #[Url(as: 'role', history: true)]
    public string $roleFilter = 'all';

    #[Url(as: 'module', history: true)]
    public string $moduleFilter = 'all';

    #[Url(as: 'action', history: true)]
    public string $actionFilter = 'all';

    #[Url(as: 'start_date', history: true)]
    public string $startDate = '';

    #[Url(as: 'end_date', history: true)]
    public string $endDate = '';

    #[Url(as: 'per_page', history: true)]
    public int $perPage = 25;

    public bool $showFilterModal = false;

    public bool $showDetailModal = false;

    public ?int $selectedLogId = null;

    public function mount(): void
    {
        Gate::authorize('access-admin-panel');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedActorFilter(): void
    {
        $this->resetPage();
    }

    public function updatedRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedModuleFilter(): void
    {
        $this->resetPage();
    }

    public function updatedActionFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStartDate(): void
    {
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function auditLogs(): LengthAwarePaginator
    {
        $user = $this->currentUser();
        $query = AuditLog::query()->visibleTo($user)->with('user');

        if (trim($this->search) !== '') {
            $term = trim($this->search);
            $query->where(function (Builder $q) use ($term) {
                $q->where('action', 'like', "%{$term}%")
                    ->orWhere('description', 'like', "%{$term}%")
                    ->orWhere('entity_type', 'like', "%{$term}%")
                    ->orWhere('ip_address', 'like', "%{$term}%")
                    ->orWhereHas('user', function (Builder $uq) use ($term) {
                        $uq->where('email', 'like', "%{$term}%");
                    });
            });
        }

        if ($user?->isSuperAdmin()) {
            if ($this->actorFilter === 'system') {
                $query->whereNull('user_id');
            } elseif (is_numeric($this->actorFilter)) {
                $query->where('user_id', (int) $this->actorFilter);
            }

            if ($this->roleFilter !== 'all') {
                if ($this->roleFilter === 'system') {
                    $query->whereNull('user_id');
                } else {
                    $role = $this->roleFilter;
                    $query->whereHas('user', function (Builder $uq) use ($role) {
                        $uq->where('role', $role);
                    });
                }
            }
        }

        if ($this->moduleFilter !== 'all') {
            if ($this->moduleFilter === 'general') {
                $query->whereNull('entity_type');
            } else {
                $query->where('entity_type', $this->moduleFilter);
            }
        }

        if ($this->actionFilter !== 'all') {
            $query->where('action', $this->actionFilter);
        }

        if ($this->startDate !== '') {
            try {
                $start = Carbon::parse($this->startDate, 'Asia/Makassar')->startOfDay()->setTimezone('UTC');
                $query->where('created_at', '>=', $start);
            } catch (\Exception) {
            }
        }

        if ($this->endDate !== '') {
            try {
                $end = Carbon::parse($this->endDate, 'Asia/Makassar')->endOfDay()->setTimezone('UTC');
                $query->where('created_at', '<=', $end);
            } catch (\Exception) {
            }
        }

        return $query->latest('id')->paginate($this->perPage);
    }

    #[Computed]
    public function statistics(): array
    {
        $user = $this->currentUser();
        $base = AuditLog::query()->visibleTo($user);
        $todayStart = Carbon::now('Asia/Makassar')->startOfDay()->setTimezone('UTC');

        return [
            'total' => (clone $base)->count(),
            'today' => (clone $base)->where('created_at', '>=', $todayStart)->count(),
            'auth' => (clone $base)->where(function (Builder $q) {
                $q->where('action', 'like', '%login%')
                    ->orWhere('action', 'like', '%logout%');
            })->count(),
            'voters' => (clone $base)->where(function (Builder $q) {
                $q->where('entity_type', 'like', '%EligibleVoter%')
                    ->orWhere('action', 'like', '%eligible_voter%')
                    ->orWhere('action', 'like', '%voter%');
            })->count(),
            'elections' => (clone $base)->where(function (Builder $q) {
                $q->where('entity_type', 'like', '%Election%')
                    ->orWhere('entity_type', 'like', '%CandidatePair%')
                    ->orWhere('action', 'like', '%candidate_pair%')
                    ->orWhere('action', 'like', '%election%');
            })->count(),
        ];
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        $count = 0;
        $isSuperAdmin = $this->currentUser()?->isSuperAdmin();

        if ($isSuperAdmin && $this->actorFilter !== 'all') {
            $count++;
        }

        if ($isSuperAdmin && $this->roleFilter !== 'all') {
            $count++;
        }

        if ($this->moduleFilter !== 'all') {
            $count++;
        }

        if ($this->actionFilter !== 'all') {
            $count++;
        }

        if ($this->startDate !== '') {
            $count++;
        }

        if ($this->endDate !== '') {
            $count++;
        }

        return $count;
    }

    #[Computed]
    public function actors(): Collection
    {
        if (! $this->currentUser()?->isSuperAdmin()) {
            return collect();
        }

        $userIds = AuditLog::query()
            ->visibleTo($this->currentUser())
            ->whereNotNull('user_id')
            ->select('user_id')
            ->distinct()
            ->pluck('user_id');

        return User::whereIn('id', $userIds)->select('id', 'email', 'role')->orderBy('email')->get();
    }

    #[Computed]
    public function modules(): array
    {
        return AuditLog::query()
            ->visibleTo($this->currentUser())
            ->whereNotNull('entity_type')
            ->select('entity_type')
            ->distinct()
            ->pluck('entity_type')
            ->filter()
            ->sort()
            ->values()
            ->all();
    }

    #[Computed]
    public function availableActions(): array
    {
        return AuditLog::query()
            ->visibleTo($this->currentUser())
            ->select('action')
            ->distinct()
            ->pluck('action')
            ->filter()
            ->sort()
            ->values()
            ->all();
    }

    #[Computed]
    public function selectedLog(): ?AuditLog
    {
        if (! $this->selectedLogId) {
            return null;
        }

        return AuditLog::query()
            ->visibleTo($this->currentUser())
            ->with('user')
            ->find($this->selectedLogId);
    }

    public function openFilterModal(): void
    {
        $this->showFilterModal = true;
    }

    public function closeFilterModal(): void
    {
        $this->showFilterModal = false;
    }

    public function openDetailModal(int $id): void
    {
        $log = AuditLog::query()->visibleTo($this->currentUser())->find($id);

        if (! $log) {
            return;
        }

        $this->selectedLogId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->selectedLogId = null;
        $this->showDetailModal = false;
    }

    public function clearFilter(string $key): void
    {
        match ($key) {
            'actor' => $this->actorFilter = 'all',
            'role' => $this->roleFilter = 'all',
            'module' => $this->moduleFilter = 'all',
            'action' => $this->actionFilter = 'all',
            'start_date' => $this->startDate = '',
            'end_date' => $this->endDate = '',
            default => null,
        };

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->actorFilter = 'all';
        $this->roleFilter = 'all';
        $this->moduleFilter = 'all';
        $this->actionFilter = 'all';
        $this->startDate = '';
        $this->endDate = '';
        $this->search = '';

        $this->resetPage();
        $this->showFilterModal = false;
    }

    private function currentUser(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }

    public function render(): View
    {
        return view('livewire.admin.audit-logs.index', [
            'logs' => $this->auditLogs,
            'stats' => $this->statistics,
            'activeFilters' => $this->activeFilterCount,
            'actorList' => $this->actors,
            'moduleList' => $this->modules,
            'actionList' => $this->availableActions,
            'detail' => $this->selectedLog,
            'isSuperAdmin' => $this->currentUser()?->isSuperAdmin() ?? false,
        ]);
    }
}
