<?php

namespace App\Livewire\Admin\CandidatePairs;

use App\Models\AuditLog;
use App\Models\CandidateMember;
use App\Models\CandidatePair;
use App\Models\Election;
use App\Models\EligibleVoter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Manajemen Paslon - Panel Admin')]
class Index extends Component
{
    use WithFileUploads, WithPagination;

    #[Url(as: 'election', history: true)]
    public ?int $selectedElectionId = null;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    public ?Election $selectedElection = null;

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDetailModal = false;

    public bool $showDeleteModal = false;

    public ?int $selectedCandidatePairId = null;

    public ?CandidatePair $selectedCandidatePair = null;

    public ?int $candidate_number = null;

    public ?int $leader_id = null;

    public ?int $vice_leader_id = null;

    /** @var TemporaryUploadedFile|UploadedFile|null */
    public $photo = null;

    public ?string $existingPhoto = null;

    public bool $removePhoto = false;

    public string $vision = '';

    public string $mission = '';

    public int|bool|string $is_active = 1;

    public string $leaderSearch = '';

    public string $viceLeaderSearch = '';

    public ?EligibleVoter $selectedLeader = null;

    public ?EligibleVoter $selectedViceLeader = null;

    public function mount(): void
    {
        if ($this->selectedElectionId) {
            $this->selectedElection = Election::find($this->selectedElectionId);
        }

        if (! $this->selectedElection) {
            $current = Election::current();
            $this->selectedElection = $current ?? Election::latest('year')->latest('id')->first();
            $this->selectedElectionId = $this->selectedElection?->id;
        }
    }

    public function updatedSelectedElectionId(?int $value): void
    {
        $this->selectedElectionId = $value;
        $this->selectedElection = $value ? Election::find($value) : null;
        $this->resetPage();
        $this->resetForm();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * @return Collection<int, EligibleVoter>
     */
    #[Computed]
    public function leaderSearchResults(): Collection
    {
        $query = trim($this->leaderSearch);
        if (strlen($query) < 2) {
            return new Collection;
        }

        return EligibleVoter::with('studyProgram')
            ->where(function ($q) use ($query) {
                $q->where('nim', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%");
            })
            ->take(10)
            ->get();
    }

    /**
     * @return Collection<int, EligibleVoter>
     */
    #[Computed]
    public function viceLeaderSearchResults(): Collection
    {
        $query = trim($this->viceLeaderSearch);
        if (strlen($query) < 2) {
            return new Collection;
        }

        return EligibleVoter::with('studyProgram')
            ->where(function ($q) use ($query) {
                $q->where('nim', 'like', "%{$query}%")
                    ->orWhere('name', 'like', "%{$query}%");
            })
            ->take(10)
            ->get();
    }

    public function selectLeader(int $id): void
    {
        $this->leader_id = $id;
        $this->selectedLeader = EligibleVoter::with('studyProgram')->find($id);
        $this->leaderSearch = '';
        $this->resetValidation('leader_id');
    }

    public function clearLeader(): void
    {
        $this->leader_id = null;
        $this->selectedLeader = null;
        $this->leaderSearch = '';
    }

    public function selectViceLeader(int $id): void
    {
        $this->vice_leader_id = $id;
        $this->selectedViceLeader = EligibleVoter::with('studyProgram')->find($id);
        $this->viceLeaderSearch = '';
        $this->resetValidation('vice_leader_id');
    }

    public function clearViceLeader(): void
    {
        $this->vice_leader_id = null;
        $this->selectedViceLeader = null;
        $this->viceLeaderSearch = '';
    }

    public function openCreateModal(): void
    {
        Gate::authorize('access-admin-panel');

        if (! $this->selectedElectionId) {
            return;
        }

        $this->resetValidation();
        $this->resetForm();

        $maxNumber = CandidatePair::where('election_id', $this->selectedElectionId)->max('candidate_number');
        $this->candidate_number = $maxNumber ? ((int) $maxNumber + 1) : 1;
        $this->is_active = 1;

        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function createCandidatePair(): void
    {
        Gate::authorize('access-admin-panel');

        if (! $this->selectedElectionId) {
            throw ValidationException::withMessages([
                'candidate_number' => 'Pemilihan belum dipilih.',
            ]);
        }

        $this->validate([
            'candidate_number' => ['required', 'integer', 'min:1', 'max:999'],
            'leader_id' => ['required', 'integer', 'exists:eligible_voters,id'],
            'vice_leader_id' => ['required', 'integer', 'exists:eligible_voters,id'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'vision' => ['required', 'string', 'max:5000'],
            'mission' => ['required', 'string', 'max:5000'],
            'is_active' => ['required', 'boolean'],
        ], [
            'candidate_number.required' => 'Nomor urut paslon wajib diisi.',
            'candidate_number.integer' => 'Nomor urut harus berupa angka positif.',
            'candidate_number.min' => 'Nomor urut minimal adalah 1.',
            'leader_id.required' => 'Calon Ketua wajib dipilih dari daftar mahasiswa.',
            'leader_id.exists' => 'Data mahasiswa calon Ketua tidak valid.',
            'vice_leader_id.required' => 'Calon Wakil Ketua wajib dipilih dari daftar mahasiswa.',
            'vice_leader_id.exists' => 'Data mahasiswa calon Wakil Ketua tidak valid.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format gambar yang diperbolehkan adalah JPEG, PNG, JPG, atau WebP.',
            'photo.max' => 'Ukuran foto maksimal adalah 2MB.',
            'vision.required' => 'Visi paslon wajib diisi.',
            'mission.required' => 'Misi paslon wajib diisi.',
        ]);

        $duplicateNumber = CandidatePair::where('election_id', $this->selectedElectionId)
            ->where('candidate_number', $this->candidate_number)
            ->exists();

        if ($duplicateNumber) {
            throw ValidationException::withMessages([
                'candidate_number' => 'Nomor urut tersebut sudah digunakan pada PEMIRA ini.',
            ]);
        }

        if ($this->leader_id === $this->vice_leader_id) {
            throw ValidationException::withMessages([
                'vice_leader_id' => 'Ketua dan Wakil harus merupakan mahasiswa yang berbeda.',
            ]);
        }

        /** @var CandidateMember|null $existingLeaderMember */
        $existingLeaderMember = CandidateMember::where('election_id', $this->selectedElectionId)
            ->where('eligible_voter_id', $this->leader_id)
            ->with(['candidatePair', 'eligibleVoter'])
            ->first();

        if ($existingLeaderMember) {
            $leaderName = $existingLeaderMember->eligibleVoter->name;
            $pairNum = $existingLeaderMember->candidatePair?->formattedNumber() ?? 'lain';
            throw ValidationException::withMessages([
                'leader_id' => "Mahasiswa ({$leaderName}) sudah terdaftar sebagai anggota Paslon {$pairNum} pada PEMIRA ini.",
            ]);
        }

        /** @var CandidateMember|null $existingViceMember */
        $existingViceMember = CandidateMember::where('election_id', $this->selectedElectionId)
            ->where('eligible_voter_id', $this->vice_leader_id)
            ->with(['candidatePair', 'eligibleVoter'])
            ->first();

        if ($existingViceMember) {
            $viceName = $existingViceMember->eligibleVoter->name;
            $pairNum = $existingViceMember->candidatePair?->formattedNumber() ?? 'lain';
            throw ValidationException::withMessages([
                'vice_leader_id' => "Mahasiswa ({$viceName}) sudah terdaftar sebagai anggota Paslon {$pairNum} pada PEMIRA ini.",
            ]);
        }

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('candidate-photos', 'public');
        }

        DB::transaction(function () use ($photoPath) {
            $pair = CandidatePair::create([
                'election_id' => $this->selectedElectionId,
                'candidate_number' => (int) $this->candidate_number,
                'photo' => $photoPath,
                'vision' => $this->vision,
                'mission' => $this->mission,
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);

            CandidateMember::create([
                'election_id' => $this->selectedElectionId,
                'candidate_pair_id' => $pair->id,
                'eligible_voter_id' => $this->leader_id,
                'position' => 'ketua',
            ]);

            CandidateMember::create([
                'election_id' => $this->selectedElectionId,
                'candidate_pair_id' => $pair->id,
                'eligible_voter_id' => $this->vice_leader_id,
                'position' => 'wakil',
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'create_candidate_pair',
                'entity_type' => 'CandidatePair',
                'entity_id' => (string) $pair->id,
                'description' => "Membuat Paslon {$pair->formattedNumber()} pada PEMIRA '{$this->selectedElection?->name}'",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => [
                    'election_id' => $this->selectedElectionId,
                    'candidate_number' => $pair->candidate_number,
                    'leader_id' => $this->leader_id,
                    'vice_leader_id' => $this->vice_leader_id,
                ],
            ]);
        });

        session()->flash('success', "Paslon {$this->candidate_number} berhasil ditambahkan.");

        $this->closeCreateModal();
        $this->resetPage();
    }

    public function openEditModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $pair = CandidatePair::with(['candidateMembers.eligibleVoter.studyProgram'])->findOrFail($id);

        $this->resetValidation();
        $this->resetForm();

        $this->selectedCandidatePairId = $pair->id;
        $this->selectedCandidatePair = $pair;
        $this->candidate_number = $pair->candidate_number;
        $this->vision = $pair->vision;
        $this->mission = $pair->mission;
        $this->is_active = $pair->is_active ? 1 : 0;
        $this->existingPhoto = $pair->photo;
        $this->photo = null;
        $this->removePhoto = false;

        $leaderMember = $pair->candidateMembers->firstWhere('position', 'ketua');
        if ($leaderMember instanceof CandidateMember) {
            $this->leader_id = $leaderMember->eligible_voter_id;
            $this->selectedLeader = $leaderMember->eligibleVoter;
        }

        $viceMember = $pair->candidateMembers->firstWhere('position', 'wakil');
        if ($viceMember instanceof CandidateMember) {
            $this->vice_leader_id = $viceMember->eligible_voter_id;
            $this->selectedViceLeader = $viceMember->eligibleVoter;
        }

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function cancelNewPhoto(): void
    {
        $this->photo = null;
    }

    public function updateCandidatePair(): void
    {
        Gate::authorize('access-admin-panel');

        if (! $this->selectedCandidatePairId) {
            return;
        }

        $pair = CandidatePair::findOrFail($this->selectedCandidatePairId);

        $this->validate([
            'candidate_number' => ['required', 'integer', 'min:1', 'max:999'],
            'leader_id' => ['required', 'integer', 'exists:eligible_voters,id'],
            'vice_leader_id' => ['required', 'integer', 'exists:eligible_voters,id'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'vision' => ['required', 'string', 'max:5000'],
            'mission' => ['required', 'string', 'max:5000'],
            'is_active' => ['required', 'boolean'],
        ], [
            'candidate_number.required' => 'Nomor urut paslon wajib diisi.',
            'candidate_number.integer' => 'Nomor urut harus berupa angka positif.',
            'candidate_number.min' => 'Nomor urut minimal adalah 1.',
            'leader_id.required' => 'Calon Ketua wajib dipilih dari daftar mahasiswa.',
            'leader_id.exists' => 'Data mahasiswa calon Ketua tidak valid.',
            'vice_leader_id.required' => 'Calon Wakil Ketua wajib dipilih dari daftar mahasiswa.',
            'vice_leader_id.exists' => 'Data mahasiswa calon Wakil Ketua tidak valid.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format gambar yang diperbolehkan adalah JPEG, PNG, JPG, atau WebP.',
            'photo.max' => 'Ukuran foto maksimal adalah 2MB.',
            'vision.required' => 'Visi paslon wajib diisi.',
            'mission.required' => 'Misi paslon wajib diisi.',
        ]);

        $duplicateNumber = CandidatePair::where('election_id', $pair->election_id)
            ->where('candidate_number', $this->candidate_number)
            ->where('id', '!=', $pair->id)
            ->exists();

        if ($duplicateNumber) {
            throw ValidationException::withMessages([
                'candidate_number' => 'Nomor urut tersebut sudah digunakan pada PEMIRA ini.',
            ]);
        }

        if ($this->leader_id === $this->vice_leader_id) {
            throw ValidationException::withMessages([
                'vice_leader_id' => 'Ketua dan Wakil harus merupakan mahasiswa yang berbeda.',
            ]);
        }

        /** @var CandidateMember|null $existingLeaderMember */
        $existingLeaderMember = CandidateMember::where('election_id', $pair->election_id)
            ->where('eligible_voter_id', $this->leader_id)
            ->where('candidate_pair_id', '!=', $pair->id)
            ->with(['candidatePair', 'eligibleVoter'])
            ->first();

        if ($existingLeaderMember) {
            $leaderName = $existingLeaderMember->eligibleVoter->name;
            $pairNum = $existingLeaderMember->candidatePair?->formattedNumber() ?? 'lain';
            throw ValidationException::withMessages([
                'leader_id' => "Mahasiswa ({$leaderName}) sudah terdaftar sebagai anggota Paslon {$pairNum} pada PEMIRA ini.",
            ]);
        }

        /** @var CandidateMember|null $existingViceMember */
        $existingViceMember = CandidateMember::where('election_id', $pair->election_id)
            ->where('eligible_voter_id', $this->vice_leader_id)
            ->where('candidate_pair_id', '!=', $pair->id)
            ->with(['candidatePair', 'eligibleVoter'])
            ->first();

        if ($existingViceMember) {
            $viceName = $existingViceMember->eligibleVoter->name;
            $pairNum = $existingViceMember->candidatePair?->formattedNumber() ?? 'lain';
            throw ValidationException::withMessages([
                'vice_leader_id' => "Mahasiswa ({$viceName}) sudah terdaftar sebagai anggota Paslon {$pairNum} pada PEMIRA ini.",
            ]);
        }

        $oldPhotoToDelete = null;
        $photoPath = $pair->photo;

        if ($this->removePhoto) {
            $oldPhotoToDelete = $pair->photo;
            $photoPath = null;
        }

        if ($this->photo) {
            $oldPhotoToDelete = $pair->photo;
            $photoPath = $this->photo->store('candidate-photos', 'public');
        }

        DB::transaction(function () use ($pair, $photoPath) {
            $pair->update([
                'candidate_number' => (int) $this->candidate_number,
                'photo' => $photoPath,
                'vision' => $this->vision,
                'mission' => $this->mission,
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);

            CandidateMember::updateOrCreate(
                [
                    'candidate_pair_id' => $pair->id,
                    'position' => 'ketua',
                ],
                [
                    'election_id' => $pair->election_id,
                    'eligible_voter_id' => $this->leader_id,
                ]
            );

            CandidateMember::updateOrCreate(
                [
                    'candidate_pair_id' => $pair->id,
                    'position' => 'wakil',
                ],
                [
                    'election_id' => $pair->election_id,
                    'eligible_voter_id' => $this->vice_leader_id,
                ]
            );

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'update_candidate_pair',
                'entity_type' => 'CandidatePair',
                'entity_id' => (string) $pair->id,
                'description' => "Memperbarui data Paslon {$pair->formattedNumber()} pada PEMIRA '{$this->selectedElection?->name}'",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => [
                    'election_id' => $pair->election_id,
                    'candidate_number' => $pair->candidate_number,
                    'leader_id' => $this->leader_id,
                    'vice_leader_id' => $this->vice_leader_id,
                    'is_active' => $pair->is_active,
                ],
            ]);
        });

        if ($oldPhotoToDelete && $oldPhotoToDelete !== $photoPath && Storage::disk('public')->exists($oldPhotoToDelete)) {
            Storage::disk('public')->delete($oldPhotoToDelete);
        }

        session()->flash('success', "Paslon {$pair->formattedNumber()} berhasil diperbarui.");

        $this->closeEditModal();
    }

    public function openDetailModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $this->selectedCandidatePair = CandidatePair::with([
            'election',
            'candidateMembers.eligibleVoter.studyProgram',
        ])->findOrFail($id);

        $this->selectedCandidatePairId = $this->selectedCandidatePair->id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedCandidatePair = null;
        $this->selectedCandidatePairId = null;
    }

    public function openDeleteModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $this->selectedCandidatePair = CandidatePair::with([
            'candidateMembers.eligibleVoter',
        ])->findOrFail($id);

        $this->selectedCandidatePairId = $this->selectedCandidatePair->id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->selectedCandidatePair = null;
        $this->selectedCandidatePairId = null;
    }

    public function deleteCandidatePair(): void
    {
        Gate::authorize('access-admin-panel');

        if (! $this->selectedCandidatePairId) {
            return;
        }

        $pair = CandidatePair::findOrFail($this->selectedCandidatePairId);

        if ($pair->hasBallots()) {
            session()->flash('error', 'Paslon tidak dapat dihapus karena sudah digunakan dalam data pemilihan.');
            $this->closeDeleteModal();

            return;
        }

        $pairNumber = $pair->formattedNumber();
        $pairId = $pair->id;
        $electionId = $pair->election_id;

        if ($pair->photo && Storage::disk('public')->exists($pair->photo)) {
            Storage::disk('public')->delete($pair->photo);
        }

        $pair->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_candidate_pair',
            'entity_type' => 'CandidatePair',
            'entity_id' => (string) $pairId,
            'description' => "Menghapus Paslon {$pairNumber} dari PEMIRA '{$this->selectedElection?->name}'",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'election_id' => $electionId,
                'candidate_number' => $pairNumber,
            ],
        ]);

        session()->flash('success', "Paslon {$pairNumber} berhasil dihapus.");

        $this->closeDeleteModal();
        $this->resetPage();
    }

    public function toggleStatus(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $pair = CandidatePair::findOrFail($id);
        $pair->is_active = ! $pair->is_active;
        $pair->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'toggle_candidate_pair_status',
            'entity_type' => 'CandidatePair',
            'entity_id' => (string) $pair->id,
            'description' => 'Mengubah status Paslon '.$pair->formattedNumber().' menjadi '.($pair->is_active ? 'Aktif' : 'Tidak Aktif'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'candidate_pair_id' => $pair->id,
                'is_active' => $pair->is_active,
            ],
        ]);

        session()->flash('success', 'Status Paslon '.$pair->formattedNumber().' berhasil diubah menjadi '.($pair->is_active ? 'Aktif' : 'Tidak Aktif').'.');
    }

    protected function resetForm(): void
    {
        $this->selectedCandidatePairId = null;
        $this->selectedCandidatePair = null;
        $this->candidate_number = null;
        $this->leader_id = null;
        $this->vice_leader_id = null;
        $this->photo = null;
        $this->existingPhoto = null;
        $this->removePhoto = false;
        $this->vision = '';
        $this->mission = '';
        $this->is_active = 1;
        $this->leaderSearch = '';
        $this->viceLeaderSearch = '';
        $this->selectedLeader = null;
        $this->selectedViceLeader = null;
    }

    public function render(): View
    {
        $availableElections = Election::query()
            ->orderByDesc('year')
            ->orderByDesc('id')
            ->get();

        $candidatePairs = new Collection;

        if ($this->selectedElectionId) {
            $query = CandidatePair::query()
                ->where('election_id', $this->selectedElectionId)
                ->with([
                    'candidateMembers.eligibleVoter.studyProgram',
                ]);

            if (trim($this->search) !== '') {
                $term = '%'.trim($this->search).'%';
                $query->where(function ($q) use ($term) {
                    $q->where('candidate_number', 'like', $term)
                        ->orWhereHas('candidateMembers.eligibleVoter', function ($sub) use ($term) {
                            $sub->where('name', 'like', $term)
                                ->orWhere('nim', 'like', $term);
                        });
                });
            }

            $candidatePairs = $query->orderBy('candidate_number', 'asc')->get();
        }

        return view('livewire.admin.candidate-pairs.index', [
            'availableElections' => $availableElections,
            'candidatePairs' => $candidatePairs,
        ]);
    }
}
