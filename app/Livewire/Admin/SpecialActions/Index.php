<?php

namespace App\Livewire\Admin\SpecialActions;

use App\Models\AuditLog;
use App\Models\SystemSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin', ['heading' => 'TINDAKAN KHUSUS'])]
#[Title('Tindakan Khusus — PEMIRA PNB')]
class Index extends Component
{
    public bool $isMaintenanceMode = false;

    public bool $showConfirmModal = false;

    public bool $targetState = false;

    public function mount(): void
    {
        Gate::authorize('super-admin-only');
        $this->isMaintenanceMode = SystemSetting::isMaintenanceMode();
    }

    #[Computed]
    public function recentMaintenanceLogs(): Collection
    {
        return AuditLog::query()
            ->whereIn('action', ['maintenance_mode_enabled', 'maintenance_mode_disabled'])
            ->with('user')
            ->latest('id')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function lastMaintenanceLog(): ?AuditLog
    {
        return AuditLog::query()
            ->whereIn('action', ['maintenance_mode_enabled', 'maintenance_mode_disabled'])
            ->with('user')
            ->latest('id')
            ->first();
    }

    public function requestToggle(bool $state): void
    {
        $this->targetState = $state;
        $this->showConfirmModal = true;
    }

    public function cancelToggle(): void
    {
        $this->showConfirmModal = false;
    }

    public function confirmToggle(): void
    {
        Gate::authorize('super-admin-only');

        $user = Auth::user();

        SystemSetting::setMaintenanceMode($this->targetState);
        $this->isMaintenanceMode = $this->targetState;

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $this->targetState ? 'maintenance_mode_enabled' : 'maintenance_mode_disabled',
            'entity_type' => 'SystemSetting',
            'entity_id' => null,
            'description' => $this->targetState
                ? 'Super Admin mengaktifkan mode pemeliharaan (Maintenance Mode)'
                : 'Super Admin menonaktifkan mode pemeliharaan (Maintenance Mode)',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'maintenance_mode' => $this->targetState,
                'actor_email' => $user?->email,
            ],
        ]);

        session()->flash('success', $this->targetState
            ? 'Mode pemeliharaan berhasil diaktifkan. Pengguna selain Super Admin kini diarahkan ke halaman pemeliharaan.'
            : 'Mode pemeliharaan berhasil dinonaktifkan. Akses website kembali dibuka normal.');

        $this->showConfirmModal = false;
    }

    public function render(): View
    {
        return view('livewire.admin.special-actions.index', [
            'recentLogs' => $this->recentMaintenanceLogs,
            'lastLog' => $this->lastMaintenanceLog,
        ]);
    }
}
