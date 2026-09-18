<?php

namespace App\Services\Dashboard;

use App\Models\User;

interface DashboardDataProviderInterface
{
    public function getElectionData(): ?array;

    public function getParticipationStats(): array;

    public function getParticipationVisualization(): array;

    public function getProgramParticipation(): array;

    public function getDepartmentParticipation(): array;

    public function getRecentActivities(): array;

    public function getSystemInfo(?User $user = null): ?array;
}
