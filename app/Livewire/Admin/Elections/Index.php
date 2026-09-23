<?php

namespace App\Livewire\Admin\Elections;

use App\Models\AuditLog;
use App\Models\Election;
use App\Models\ScheduleChangeRequest;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Manajemen PEMIRA - Panel Admin')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'status', history: true)]
    public string $statusFilter = '';

    #[Url(as: 'tahun', history: true)]
    public string $yearFilter = '';

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDetailModal = false;

    public bool $showDeleteModal = false;

    public ?int $selectedElectionId = null;

    public ?Election $selectedElection = null;

    public array $detailHistoricalSummary = [];

    public string $name = '';

    public ?int $year = null;

    public string $registration_start_at = '';

    public string $registration_end_at = '';

    public string $voting_start_at = '';

    public string $voting_end_at = '';

    public ?string $originalRegistrationStart = null;

    public ?string $originalRegistrationEnd = null;

    public ?string $originalVotingStart = null;

    public ?string $originalVotingEnd = null;

    public bool $isVotingActive = false;

    public string $scheduleChangeReason = '';

    public ?ScheduleChangeRequest $activeScheduleRequest = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:2020', 'max:2099'],
            'registration_start_at' => ['required', 'date'],
            'registration_end_at' => ['required', 'date', 'after:registration_start_at'],
            'voting_start_at' => ['required', 'date', 'after_or_equal:registration_end_at'],
            'voting_end_at' => ['required', 'date', 'after:voting_start_at'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama PEMIRA wajib diisi.',
            'name.max' => 'Nama PEMIRA maksimal 255 karakter.',
            'year.required' => 'Tahun pemilihan wajib diisi.',
            'year.integer' => 'Tahun harus berupa angka yang valid.',
            'year.min' => 'Tahun minimal adalah 2020.',
            'year.max' => 'Tahun maksimal adalah 2099.',
            'registration_start_at.required' => 'Waktu mulai pendaftaran wajib diisi.',
            'registration_start_at.date' => 'Format waktu mulai pendaftaran tidak valid.',
            'registration_end_at.required' => 'Waktu akhir pendaftaran wajib diisi.',
            'registration_end_at.date' => 'Format waktu akhir pendaftaran tidak valid.',
            'registration_end_at.after' => 'Waktu akhir pendaftaran harus setelah waktu mulai pendaftaran.',
            'voting_start_at.required' => 'Waktu mulai voting wajib diisi.',
            'voting_start_at.date' => 'Format waktu mulai voting tidak valid.',
            'voting_start_at.after_or_equal' => 'Waktu mulai voting tidak boleh mendahului penutupan pendaftaran.',
            'voting_end_at.required' => 'Waktu akhir voting wajib diisi.',
            'voting_end_at.date' => 'Format waktu akhir voting tidak valid.',
            'voting_end_at.after' => 'Waktu akhir voting harus setelah waktu mulai voting.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingYearFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        Gate::authorize('access-admin-panel');

        $this->resetValidation();
        $this->resetForm();

        $now = Carbon::now();
        $this->year = (int) $now->year;
        $this->registration_start_at = $now->copy()->addDays(1)->setHour(8)->setMinute(0)->format('Y-m-d\TH:i');
        $this->registration_end_at = $now->copy()->addDays(5)->setHour(16)->setMinute(0)->format('Y-m-d\TH:i');
        $this->voting_start_at = $now->copy()->addDays(7)->setHour(8)->setMinute(0)->format('Y-m-d\TH:i');
        $this->voting_end_at = $now->copy()->addDays(7)->setHour(16)->setMinute(0)->format('Y-m-d\TH:i');

        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function createElection(): void
    {
        Gate::authorize('access-admin-panel');

        $validated = $this->validate();

        $slug = $this->generateUniqueSlug($validated['name'], (int) $validated['year']);

        Election::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'year' => (int) $validated['year'],
            'registration_start_at' => Carbon::parse($validated['registration_start_at']),
            'registration_end_at' => Carbon::parse($validated['registration_end_at']),
            'voting_start_at' => Carbon::parse($validated['voting_start_at']),
            'voting_end_at' => Carbon::parse($validated['voting_end_at']),
        ]);

        session()->flash('success', "PEMIRA '{$validated['name']}' berhasil dibuat.");

        $this->closeCreateModal();
        $this->resetPage();
    }

    public function openEditModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $election = Election::findOrFail($id);

        $this->resetValidation();
        $this->resetForm();

        $this->selectedElectionId = $election->id;
        $this->selectedElection = $election;
        $this->name = $election->name;
        $this->year = $election->year;
        $this->registration_start_at = $election->registration_start_at->format('Y-m-d\TH:i');
        $this->registration_end_at = $election->registration_end_at->format('Y-m-d\TH:i');
        $this->voting_start_at = $election->voting_start_at->format('Y-m-d\TH:i');
        $this->voting_end_at = $election->voting_end_at->format('Y-m-d\TH:i');

        $this->originalRegistrationStart = $this->registration_start_at;
        $this->originalRegistrationEnd = $this->registration_end_at;
        $this->originalVotingStart = $this->voting_start_at;
        $this->originalVotingEnd = $this->voting_end_at;
        $this->isVotingActive = $election->isVotingActive();
        $this->activeScheduleRequest = ScheduleChangeRequest::where('election_id', $election->id)->pending()->first();
        $this->scheduleChangeReason = '';

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function updateElection(): void
    {
        Gate::authorize('access-admin-panel');

        if (! $this->selectedElectionId) {
            return;
        }
        $election = Election::findOrFail($this->selectedElectionId);
        $isSuperAdmin = Auth::user()?->isSuperAdmin() ?? false;

        $hasAttemptedVotingChange = ($this->voting_start_at !== $this->originalVotingStart)
            || ($this->voting_end_at !== $this->originalVotingEnd);
        $hasAttemptedRegistrationChange = ($this->registration_start_at !== $this->originalRegistrationStart)
            || ($this->registration_end_at !== $this->originalRegistrationEnd);
        $hasAttemptedScheduleChange = $hasAttemptedVotingChange || $hasAttemptedRegistrationChange;

        if (! $isSuperAdmin && $hasAttemptedScheduleChange) {
            $hasPendingRequest = ScheduleChangeRequest::where('election_id', $election->id)
                ->pending()
                ->exists();

            if ($hasPendingRequest) {
                throw ValidationException::withMessages([
                    'voting_start_at' => 'Pemilihan ini sudah memiliki pengajuan perubahan jadwal yang sedang menunggu persetujuan Super Admin.',
                ]);
            }

            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'year' => ['required', 'integer', 'min:2020', 'max:2099'],
                'registration_start_at' => ['required', 'date'],
                'registration_end_at' => ['required', 'date', 'after:registration_start_at'],
                'voting_start_at' => ['required', 'date', 'after_or_equal:registration_end_at'],
                'voting_end_at' => ['required', 'date', 'after:voting_start_at'],
                'scheduleChangeReason' => ['required', 'string', 'min:10', 'max:1000'],
            ], [
                'scheduleChangeReason.required' => 'Alasan perubahan jadwal wajib diisi.',
                'scheduleChangeReason.min' => 'Alasan perubahan jadwal minimal 10 karakter.',
                'scheduleChangeReason.max' => 'Alasan perubahan jadwal maksimal 1000 karakter.',
            ]);

            $newRegStart = Carbon::parse($validated['registration_start_at']);
            $newRegEnd = Carbon::parse($validated['registration_end_at']);
            $newVotingStart = Carbon::parse($validated['voting_start_at']);
            $newVotingEnd = Carbon::parse($validated['voting_end_at']);
            $slug = $this->generateUniqueSlug($validated['name'], (int) $validated['year'], $election->id);

            DB::transaction(function () use ($election, $validated, $newRegStart, $newRegEnd, $newVotingStart, $newVotingEnd, $slug) {
                $req = ScheduleChangeRequest::create([
                    'election_id' => $election->id,
                    'requested_by' => Auth::id(),
                    'old_registration_start_at' => $election->registration_start_at,
                    'old_registration_end_at' => $election->registration_end_at,
                    'new_registration_start_at' => $newRegStart,
                    'new_registration_end_at' => $newRegEnd,
                    'old_voting_start_at' => $election->voting_start_at,
                    'old_voting_end_at' => $election->voting_end_at,
                    'new_voting_start_at' => $newVotingStart,
                    'new_voting_end_at' => $newVotingEnd,
                    'reason' => trim($validated['scheduleChangeReason']),
                    'status' => 'pending',
                ]);

                AuditLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'schedule_change_requested',
                    'entity_type' => 'ScheduleChangeRequest',
                    'entity_id' => $req->id,
                    'description' => "Pengajuan perubahan jadwal diajukan untuk PEMIRA '{$election->name}'",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'metadata' => [
                        'election_id' => $election->id,
                        'old_registration_start_at' => $election->registration_start_at?->toDateTimeString(),
                        'old_registration_end_at' => $election->registration_end_at?->toDateTimeString(),
                        'new_registration_start_at' => $newRegStart->toDateTimeString(),
                        'new_registration_end_at' => $newRegEnd->toDateTimeString(),
                        'old_voting_start_at' => $election->voting_start_at?->toDateTimeString(),
                        'old_voting_end_at' => $election->voting_end_at?->toDateTimeString(),
                        'new_voting_start_at' => $newVotingStart->toDateTimeString(),
                        'new_voting_end_at' => $newVotingEnd->toDateTimeString(),
                        'reason' => $validated['scheduleChangeReason'],
                    ],
                ]);

                $election->update([
                    'name' => $validated['name'],
                    'slug' => $slug,
                    'year' => (int) $validated['year'],
                ]);
            });

            session()->flash('success', "Permohonan perubahan jadwal untuk PEMIRA '{$election->name}' berhasil diajukan dan sedang menunggu persetujuan Super Admin.");

            $this->closeEditModal();

            return;
        }

        if (! $isSuperAdmin) {
            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'year' => ['required', 'integer', 'min:2020', 'max:2099'],
            ]);

            $slug = $this->generateUniqueSlug($validated['name'], (int) $validated['year'], $election->id);

            $election->update([
                'name' => $validated['name'],
                'slug' => $slug,
                'year' => (int) $validated['year'],
            ]);
        } else {
            $validated = $this->validate();

            $slug = $this->generateUniqueSlug($validated['name'], (int) $validated['year'], $election->id);

            $election->update([
                'name' => $validated['name'],
                'slug' => $slug,
                'year' => (int) $validated['year'],
                'registration_start_at' => Carbon::parse($validated['registration_start_at']),
                'registration_end_at' => Carbon::parse($validated['registration_end_at']),
                'voting_start_at' => Carbon::parse($validated['voting_start_at']),
                'voting_end_at' => Carbon::parse($validated['voting_end_at']),
            ]);
        }

        session()->flash('success', "PEMIRA '{$election->name}' berhasil diperbarui.");

        $this->closeEditModal();
    }

    public function openDetailModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $this->selectedElection = Election::findOrFail($id);
        $this->selectedElectionId = $this->selectedElection->id;
        $this->detailHistoricalSummary = $this->selectedElection->getHistoricalDataSummary();
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedElection = null;
        $this->selectedElectionId = null;
        $this->detailHistoricalSummary = [];
    }

    public function openDeleteModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $this->selectedElection = Election::findOrFail($id);
        $this->selectedElectionId = $this->selectedElection->id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->selectedElection = null;
        $this->selectedElectionId = null;
    }

    public function deleteElection(): void
    {
        Gate::authorize('access-admin-panel');

        if (! $this->selectedElectionId) {
            return;
        }

        $election = Election::findOrFail($this->selectedElectionId);

        if ($election->hasHistoricalData()) {
            session()->flash('error', "PEMIRA '{$election->name}' tidak dapat dihapus karena telah memiliki data pemilihan (partisipasi/suara/masukan) yang harus dilindungi.");
            $this->closeDeleteModal();

            return;
        }

        $electionName = $election->name;
        $election->delete();

        session()->flash('success', "PEMIRA '{$electionName}' berhasil dihapus.");

        $this->closeDeleteModal();
        $this->resetPage();
    }

    protected function resetForm(): void
    {
        $this->selectedElectionId = null;
        $this->selectedElection = null;
        $this->name = '';
        $this->year = null;
        $this->registration_start_at = '';
        $this->registration_end_at = '';
        $this->voting_start_at = '';
        $this->voting_end_at = '';
        $this->isVotingActive = false;
        $this->originalRegistrationStart = null;
        $this->originalRegistrationEnd = null;
        $this->originalVotingStart = null;
        $this->originalVotingEnd = null;
        $this->scheduleChangeReason = '';
        $this->activeScheduleRequest = null;
    }

    protected function generateUniqueSlug(string $name, int $year, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name.'-'.$year);
        if (empty($baseSlug)) {
            $baseSlug = 'pemira-'.$year;
        }

        $slug = $baseSlug;
        $counter = 1;

        while (Election::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    public function render(): View
    {
        $query = Election::query();

        if (trim($this->search) !== '') {
            $searchTerm = '%'.trim($this->search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('slug', 'like', $searchTerm)
                    ->orWhere('year', 'like', $searchTerm);
            });
        }

        if ($this->yearFilter !== '') {
            $query->where('year', (int) $this->yearFilter);
        }

        $elections = $query->with(['scheduleChangeRequests' => fn ($q) => $q->pending()])
            ->latest('year')
            ->latest('id')
            ->paginate(10);

        $availableYears = Election::query()
            ->select('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->all();

        return view('livewire.admin.elections.index', [
            'elections' => $elections,
            'availableYears' => $availableYears,
        ]);
    }
}
