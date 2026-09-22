<?php

namespace App\Livewire\Admin\Feedbacks;

use App\Models\AuditLog;
use App\Models\Election;
use App\Models\Feedback;
use App\Models\StudyProgram;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Masukan Pemilih - Panel Admin')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'election', history: true)]
    public string $electionFilter = 'all';

    #[Url(as: 'rating', history: true)]
    public string $ratingFilter = 'all';

    #[Url(as: 'type', history: true)]
    public string $typeFilter = 'all';

    #[Url(as: 'prodi', history: true)]
    public string $studyProgramFilter = 'all';

    #[Url(as: 'start_date', history: true)]
    public string $startDate = '';

    #[Url(as: 'end_date', history: true)]
    public string $endDate = '';

    #[Url(as: 'per_page', history: true)]
    public int $perPage = 25;

    public bool $showFilterModal = false;

    public bool $showDetailModal = false;

    public bool $showDeleteModal = false;

    public ?int $selectedFeedbackId = null;

    public function mount(): void
    {
        Gate::authorize('access-admin-panel');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedElectionFilter(): void
    {
        $this->resetPage();
    }

    public function updatedRatingFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStudyProgramFilter(): void
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
    public function feedbacks(): LengthAwarePaginator
    {
        $query = Feedback::query()->with([
            'election',
            'voterAccount.eligibleVoter.studyProgram',
            'voterAccount.user',
        ]);

        if (trim($this->search) !== '') {
            $term = trim($this->search);
            $query->where(function (Builder $q) use ($term) {
                $q->where('comment', 'like', "%{$term}%")
                    ->orWhereHas('voterAccount.eligibleVoter', function (Builder $vq) use ($term) {
                        $vq->where('name', 'like', "%{$term}%")
                            ->orWhere('nim', 'like', "%{$term}%")
                            ->orWhereHas('studyProgram', function (Builder $sq) use ($term) {
                                $sq->where('name', 'like', "%{$term}%")
                                    ->orWhere('code', 'like', "%{$term}%");
                            });
                    })
                    ->orWhereHas('voterAccount.user', function (Builder $uq) use ($term) {
                        $uq->where('email', 'like', "%{$term}%");
                    });
            });
        }

        if ($this->electionFilter !== 'all' && is_numeric($this->electionFilter)) {
            $query->where('election_id', (int) $this->electionFilter);
        }

        if ($this->ratingFilter !== 'all' && is_numeric($this->ratingFilter)) {
            $query->where('rating', (int) $this->ratingFilter);
        }

        if ($this->typeFilter === 'with_comment') {
            $query->whereNotNull('comment')->where('comment', '!=', '');
        } elseif ($this->typeFilter === 'rating_only') {
            $query->where(function (Builder $q) {
                $q->whereNull('comment')->orWhere('comment', '');
            });
        }

        if ($this->studyProgramFilter !== 'all' && is_numeric($this->studyProgramFilter)) {
            $prodiId = (int) $this->studyProgramFilter;
            $query->whereHas('voterAccount.eligibleVoter', function (Builder $q) use ($prodiId) {
                $q->where('study_program_id', $prodiId);
            });
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
        $base = Feedback::query();

        return [
            'total' => (clone $base)->count(),
            'average_rating' => round((float) ((clone $base)->avg('rating') ?? 0), 1),
            'with_comment' => (clone $base)->whereNotNull('comment')->where('comment', '!=', '')->count(),
            'positive' => (clone $base)->whereIn('rating', [4, 5])->count(),
            'critical' => (clone $base)->whereIn('rating', [1, 2])->count(),
        ];
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        $count = 0;

        if ($this->electionFilter !== 'all') {
            $count++;
        }

        if ($this->ratingFilter !== 'all') {
            $count++;
        }

        if ($this->typeFilter !== 'all') {
            $count++;
        }

        if ($this->studyProgramFilter !== 'all') {
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
    public function elections(): Collection
    {
        return Election::query()->select('id', 'name', 'year')->orderByDesc('year')->orderByDesc('id')->get();
    }

    #[Computed]
    public function studyPrograms(): Collection
    {
        return StudyProgram::query()->select('id', 'name', 'code')->orderBy('name')->get();
    }

    #[Computed]
    public function selectedFeedback(): ?Feedback
    {
        if (! $this->selectedFeedbackId) {
            return null;
        }

        return Feedback::query()
            ->with([
                'election',
                'voterAccount.eligibleVoter.studyProgram',
                'voterAccount.user',
            ])
            ->find($this->selectedFeedbackId);
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
        $feedback = Feedback::find($id);

        if (! $feedback) {
            return;
        }

        $this->selectedFeedbackId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->selectedFeedbackId = null;
        $this->showDetailModal = false;
    }

    public function openDeleteModal(int $id): void
    {
        Gate::authorize('super-admin-only');

        $feedback = Feedback::find($id);

        if (! $feedback) {
            return;
        }

        $this->selectedFeedbackId = $id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->selectedFeedbackId = null;
        $this->showDeleteModal = false;
    }

    public function deleteFeedback(): void
    {
        Gate::authorize('super-admin-only');

        if (! $this->selectedFeedbackId) {
            return;
        }

        $feedback = Feedback::with('voterAccount.eligibleVoter')->find($this->selectedFeedbackId);

        if (! $feedback) {
            $this->closeDeleteModal();

            return;
        }

        $voterName = $feedback->voterAccount?->eligibleVoter?->name ?? 'Anonim';
        $voterNim = $feedback->voterAccount?->eligibleVoter?->nim ?? '-';

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'feedback_deleted',
            'entity_type' => Feedback::class,
            'entity_id' => $feedback->id,
            'description' => "Menghapus masukan pemilih #{$feedback->id} dari {$voterName} ({$voterNim}) dengan rating {$feedback->rating}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $feedback->delete();

        $this->closeDeleteModal();
        $this->resetPage();
    }

    public function clearFilter(string $key): void
    {
        match ($key) {
            'election' => $this->electionFilter = 'all',
            'rating' => $this->ratingFilter = 'all',
            'type' => $this->typeFilter = 'all',
            'prodi' => $this->studyProgramFilter = 'all',
            'start_date' => $this->startDate = '',
            'end_date' => $this->endDate = '',
            default => null,
        };

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->electionFilter = 'all';
        $this->ratingFilter = 'all';
        $this->typeFilter = 'all';
        $this->studyProgramFilter = 'all';
        $this->startDate = '';
        $this->endDate = '';
        $this->search = '';

        $this->resetPage();
        $this->showFilterModal = false;
    }

    public function render(): View
    {
        $user = Auth::user();

        return view('livewire.admin.feedbacks.index', [
            'feedbacks' => $this->feedbacks,
            'stats' => $this->statistics,
            'activeFilters' => $this->activeFilterCount,
            'electionList' => $this->elections,
            'studyProgramList' => $this->studyPrograms,
            'detail' => $this->selectedFeedback,
            'isSuperAdmin' => $user instanceof User && $user->isSuperAdmin(),
        ]);
    }
}
