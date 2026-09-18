<?php

namespace App\Livewire\Admin;

use App\Services\Dashboard\DashboardDataProviderInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard Admin - PEMIRA PNB 2026')]
class Dashboard extends Component
{
    #[Computed]
    public function electionData(): ?array
    {
        return app(DashboardDataProviderInterface::class)->getElectionData();
    }

    #[Computed]
    public function participationData(): array
    {
        return app(DashboardDataProviderInterface::class)->getParticipationStats();
    }

    #[Computed]
    public function visualizationData(): array
    {
        return app(DashboardDataProviderInterface::class)->getParticipationVisualization();
    }

    #[Computed]
    public function departmentParticipation(): array
    {
        return app(DashboardDataProviderInterface::class)->getDepartmentParticipation();
    }

    #[Computed]
    public function programParticipation(): array
    {
        return $this->departmentParticipation();
    }

    #[Computed]
    public function recentActivities(): array
    {
        return app(DashboardDataProviderInterface::class)->getRecentActivities();
    }

    #[Computed]
    public function systemInfo(): ?array
    {
        return app(DashboardDataProviderInterface::class)->getSystemInfo(Auth::user());
    }

    public function render(): View
    {
        return view('livewire.admin.dashboard');
    }
}
