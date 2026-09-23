<?php

namespace App\Livewire\Admin\Results;

use App\Models\Election;
use App\Services\Election\ElectionResultService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Hasil Perhitungan Suara - Panel Admin')]
class Index extends Component
{
    #[Url(as: 'election', history: true)]
    public ?int $selectedElectionId = null;

    public string $lastRefreshedAt = '';

    public function mount(ElectionResultService $resultService): void
    {
        Gate::authorize('access-admin-panel');

        if (! $this->selectedElectionId) {
            $current = Election::current();
            $this->selectedElectionId = $current?->id ?? $resultService->getAvailableElections()->first()?->id;
        }

        $this->lastRefreshedAt = Carbon::now('Asia/Makassar')->format('H:i:s').' WITA';
    }

    public function selectElection(int $id): void
    {
        $this->selectedElectionId = $id;
        $this->lastRefreshedAt = Carbon::now('Asia/Makassar')->format('H:i:s').' WITA';
    }

    public function refreshResults(): void
    {
        $this->lastRefreshedAt = Carbon::now('Asia/Makassar')->format('H:i:s').' WITA';
    }

    #[Computed]
    public function results(): ?array
    {
        return app(ElectionResultService::class)->getResults($this->selectedElectionId);
    }

    #[Computed]
    public function availableElections(): Collection
    {
        return app(ElectionResultService::class)->getAvailableElections();
    }

    public function render(): View
    {
        return view('livewire.admin.results.index', [
            'data' => $this->results,
            'elections' => $this->availableElections,
        ]);
    }
}
