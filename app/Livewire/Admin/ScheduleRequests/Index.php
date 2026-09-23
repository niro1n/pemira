<?php

namespace App\Livewire\Admin\ScheduleRequests;

use App\Models\AuditLog;
use App\Models\Election;
use App\Models\ScheduleChangeRequest;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['heading' => 'PENGAJUAN JADWAL'])]
#[Title('Pengajuan Jadwal — Panel Admin')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'status', history: true)]
    public string $statusFilter = '';

    #[Url(as: 'election', history: true)]
    public ?int $electionFilter = null;

    public int $perPage = 10;

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showDetailModal = false;

    public bool $showApproveModal = false;

    public bool $showRejectModal = false;

    public bool $showDeleteModal = false;

    public ?int $selectedRequestId = null;

    public ?ScheduleChangeRequest $selectedRequest = null;

    public ?int $approveRequestId = null;

    public ?int $rejectRequestId = null;

    public ?int $deleteRequestId = null;

    public ?int $election_id = null;

    public string $new_voting_start_at = '';

    public string $new_voting_end_at = '';

    public string $reason = '';

    public string $review_note = '';

    public ?string $current_voting_start_at = null;

    public ?string $current_voting_end_at = null;

    public function mount(?int $create = null, ?int $election_id = null): void
    {
        Gate::authorize('access-admin-panel');

        if ($create) {
            $this->openCreateModal($election_id);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingElectionFilter(): void
    {
        $this->resetPage();
    }

    public function updatedElectionId($value): void
    {
        if ($value) {
            $election = Election::find($value);
            if ($election) {
                $this->current_voting_start_at = $election->voting_start_at?->timezone('Asia/Makassar')->format('d M Y, H:i').' WITA';
                $this->current_voting_end_at = $election->voting_end_at?->timezone('Asia/Makassar')->format('d M Y, H:i').' WITA';

                if (! $this->new_voting_start_at && $election->voting_start_at) {
                    $this->new_voting_start_at = $election->voting_start_at->timezone('Asia/Makassar')->format('Y-m-d\TH:i');
                }
                if (! $this->new_voting_end_at && $election->voting_end_at) {
                    $this->new_voting_end_at = $election->voting_end_at->timezone('Asia/Makassar')->format('Y-m-d\TH:i');
                }
            }
        } else {
            $this->current_voting_start_at = null;
            $this->current_voting_end_at = null;
        }
    }

    public function openCreateModal(?int $electionId = null): void
    {
        Gate::authorize('access-admin-panel');

        $this->resetForm();
        $this->resetValidation();

        $selectedElection = null;
        if ($electionId) {
            $selectedElection = Election::find($electionId);
        }

        if (! $selectedElection) {
            $selectedElection = Election::current() ?? Election::latest('id')->first();
        }

        if ($selectedElection) {
            $this->election_id = $selectedElection->id;
            $this->current_voting_start_at = $selectedElection->voting_start_at?->timezone('Asia/Makassar')->format('d M Y, H:i').' WITA';
            $this->current_voting_end_at = $selectedElection->voting_end_at?->timezone('Asia/Makassar')->format('d M Y, H:i').' WITA';
            $this->new_voting_start_at = $selectedElection->voting_start_at ? $selectedElection->voting_start_at->timezone('Asia/Makassar')->format('Y-m-d\TH:i') : '';
            $this->new_voting_end_at = $selectedElection->voting_end_at ? $selectedElection->voting_end_at->timezone('Asia/Makassar')->format('Y-m-d\TH:i') : '';
        }

        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function createRequest(): void
    {
        Gate::authorize('access-admin-panel');

        $validated = $this->validate([
            'election_id' => ['required', 'integer', 'exists:elections,id'],
            'new_voting_start_at' => ['required', 'date'],
            'new_voting_end_at' => ['required', 'date', 'after:new_voting_start_at'],
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'election_id.required' => 'Pemilihan wajib dipilih.',
            'new_voting_start_at.required' => 'Waktu mulai voting baru wajib diisi.',
            'new_voting_end_at.required' => 'Waktu selesai voting baru wajib diisi.',
            'new_voting_end_at.after' => 'Waktu selesai voting baru harus setelah waktu mulai voting.',
            'reason.required' => 'Alasan perubahan jadwal wajib diisi.',
            'reason.min' => 'Alasan perubahan jadwal minimal 10 karakter.',
            'reason.max' => 'Alasan perubahan jadwal maksimal 1000 karakter.',
        ]);

        $hasPending = ScheduleChangeRequest::where('election_id', $this->election_id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            throw ValidationException::withMessages([
                'election_id' => 'Masih terdapat permohonan perubahan jadwal yang berstatus menunggu persetujuan (pending) untuk pemilihan ini.',
            ]);
        }

        $election = Election::findOrFail($this->election_id);
        $newStart = Carbon::parse($validated['new_voting_start_at']);
        $newEnd = Carbon::parse($validated['new_voting_end_at']);

        if ($election->registration_end_at && $newStart->lt($election->registration_end_at)) {
            throw ValidationException::withMessages([
                'new_voting_start_at' => 'Waktu mulai voting baru tidak boleh mendahului penutupan pendaftaran pemilih ('.$election->registration_end_at->timezone('Asia/Makassar')->format('d/m/Y H:i').' WITA).',
            ]);
        }

        $request = ScheduleChangeRequest::create([
            'election_id' => $election->id,
            'requested_by' => Auth::id(),
            'old_voting_start_at' => $election->voting_start_at,
            'old_voting_end_at' => $election->voting_end_at,
            'new_voting_start_at' => $newStart,
            'new_voting_end_at' => $newEnd,
            'reason' => trim($validated['reason']),
            'status' => 'pending',
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'schedule_change_requested',
            'entity_type' => 'ScheduleChangeRequest',
            'entity_id' => $request->id,
            'description' => "Pengajuan perubahan jadwal voting diajukan untuk PEMIRA '{$election->name}'",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'election_id' => $election->id,
                'old_voting_start_at' => $election->voting_start_at?->toDateTimeString(),
                'old_voting_end_at' => $election->voting_end_at?->toDateTimeString(),
                'new_voting_start_at' => $newStart->toDateTimeString(),
                'new_voting_end_at' => $newEnd->toDateTimeString(),
                'reason' => $validated['reason'],
            ],
        ]);

        session()->flash('success', 'Pengajuan perubahan jadwal berhasil dikirim.');

        $this->closeCreateModal();
        $this->resetPage();
    }

    public function openEditModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $request = ScheduleChangeRequest::with('election')->findOrFail($id);

        if (! $request->isPending()) {
            session()->flash('error', 'Pengajuan ini tidak dapat diedit karena telah diproses.');

            return;
        }

        if (! Auth::user()->isSuperAdmin() && $request->requested_by !== Auth::id()) {
            abort(403);
        }

        $this->resetValidation();
        $this->selectedRequestId = $request->id;
        $this->election_id = $request->election_id;
        $this->new_voting_start_at = $request->new_voting_start_at->timezone('Asia/Makassar')->format('Y-m-d\TH:i');
        $this->new_voting_end_at = $request->new_voting_end_at->timezone('Asia/Makassar')->format('Y-m-d\TH:i');
        $this->reason = $request->reason;
        $this->current_voting_start_at = $request->old_voting_start_at?->timezone('Asia/Makassar')->format('d M Y, H:i').' WITA';
        $this->current_voting_end_at = $request->old_voting_end_at?->timezone('Asia/Makassar')->format('d M Y, H:i').' WITA';

        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function updateRequest(): void
    {
        Gate::authorize('access-admin-panel');

        if (! $this->selectedRequestId) {
            return;
        }

        $request = ScheduleChangeRequest::with('election')->findOrFail($this->selectedRequestId);

        if (! $request->isPending()) {
            session()->flash('error', 'Pengajuan ini tidak dapat diedit karena telah diproses.');
            $this->closeEditModal();

            return;
        }

        if (! Auth::user()->isSuperAdmin() && $request->requested_by !== Auth::id()) {
            abort(403);
        }

        $validated = $this->validate([
            'new_voting_start_at' => ['required', 'date'],
            'new_voting_end_at' => ['required', 'date', 'after:new_voting_start_at'],
            'reason' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'new_voting_start_at.required' => 'Waktu mulai voting baru wajib diisi.',
            'new_voting_end_at.required' => 'Waktu selesai voting baru wajib diisi.',
            'new_voting_end_at.after' => 'Waktu selesai voting baru harus setelah waktu mulai voting.',
            'reason.required' => 'Alasan perubahan jadwal wajib diisi.',
            'reason.min' => 'Alasan perubahan jadwal minimal 10 karakter.',
            'reason.max' => 'Alasan perubahan jadwal maksimal 1000 karakter.',
        ]);

        $election = $request->election;
        $newStart = Carbon::parse($validated['new_voting_start_at']);
        $newEnd = Carbon::parse($validated['new_voting_end_at']);

        if ($election && $election->registration_end_at && $newStart->lt($election->registration_end_at)) {
            throw ValidationException::withMessages([
                'new_voting_start_at' => 'Waktu mulai voting baru tidak boleh mendahului penutupan pendaftaran pemilih ('.$election->registration_end_at->timezone('Asia/Makassar')->format('d/m/Y H:i').' WITA).',
            ]);
        }

        $request->update([
            'new_voting_start_at' => $newStart,
            'new_voting_end_at' => $newEnd,
            'reason' => trim($validated['reason']),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'schedule_change_updated',
            'entity_type' => 'ScheduleChangeRequest',
            'entity_id' => $request->id,
            'description' => "Pengajuan perubahan jadwal voting diperbarui untuk PEMIRA '{$election?->name}'",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'election_id' => $request->election_id,
                'new_voting_start_at' => $newStart->toDateTimeString(),
                'new_voting_end_at' => $newEnd->toDateTimeString(),
                'reason' => $validated['reason'],
            ],
        ]);

        session()->flash('success', 'Pengajuan perubahan jadwal berhasil diperbarui.');

        $this->closeEditModal();
    }

    public function openDeleteModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $request = ScheduleChangeRequest::findOrFail($id);

        if (! $request->isPending()) {
            session()->flash('error', 'Pengajuan ini tidak dapat dibatalkan karena telah diproses.');

            return;
        }

        if (! Auth::user()->isSuperAdmin() && $request->requested_by !== Auth::id()) {
            abort(403);
        }

        $this->deleteRequestId = $request->id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deleteRequestId = null;
    }

    public function deleteRequest(): void
    {
        Gate::authorize('access-admin-panel');

        if (! $this->deleteRequestId) {
            return;
        }

        $request = ScheduleChangeRequest::with('election')->findOrFail($this->deleteRequestId);

        if (! $request->isPending()) {
            session()->flash('error', 'Pengajuan ini tidak dapat dibatalkan karena telah diproses.');
            $this->closeDeleteModal();

            return;
        }

        if (! Auth::user()->isSuperAdmin() && $request->requested_by !== Auth::id()) {
            abort(403);
        }

        $electionName = $request->election?->name ?? 'PEMIRA';
        $requestId = $request->id;

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'schedule_change_deleted',
            'entity_type' => 'ScheduleChangeRequest',
            'entity_id' => $requestId,
            'description' => "Membatalkan pengajuan perubahan jadwal PEMIRA '{$electionName}'",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $request->delete();

        session()->flash('success', 'Pengajuan perubahan jadwal berhasil dibatalkan.');

        $this->closeDeleteModal();
        $this->resetPage();
    }

    public function openDetailModal(int $id): void
    {
        Gate::authorize('access-admin-panel');

        $this->selectedRequest = ScheduleChangeRequest::with(['election', 'requester', 'reviewer'])->findOrFail($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->selectedRequest = null;
    }

    public function openApproveModal(int $id): void
    {
        Gate::authorize('super-admin-only');

        $request = ScheduleChangeRequest::with('election')->findOrFail($id);

        if (! $request->isPending()) {
            session()->flash('error', 'Pengajuan ini tidak dapat disetujui karena telah diproses sebelumnya.');

            return;
        }

        $this->approveRequestId = $request->id;
        $this->showApproveModal = true;
    }

    public function closeApproveModal(): void
    {
        $this->showApproveModal = false;
        $this->approveRequestId = null;
    }

    public function approveRequest(): void
    {
        Gate::authorize('super-admin-only');

        if (! $this->approveRequestId) {
            return;
        }

        DB::transaction(function () {
            $req = ScheduleChangeRequest::where('id', $this->approveRequestId)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $req->isPending()) {
                throw ValidationException::withMessages([
                    'general' => 'Pengajuan ini telah diproses sebelumnya dan tidak dapat disetujui kembali.',
                ]);
            }

            $election = Election::where('id', $req->election_id)
                ->lockForUpdate()
                ->firstOrFail();

            $updateData = [];
            if ($req->new_registration_start_at) {
                $updateData['registration_start_at'] = $req->new_registration_start_at;
                $updateData['registration_end_at'] = $req->new_registration_end_at;
            }
            if ($req->new_voting_start_at) {
                $updateData['voting_start_at'] = $req->new_voting_start_at;
                $updateData['voting_end_at'] = $req->new_voting_end_at;
            }
            $election->update($updateData);

            $req->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => Carbon::now(),
                'reviewed_at' => Carbon::now(),
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'schedule_change_approved',
                'entity_type' => 'ScheduleChangeRequest',
                'entity_id' => $req->id,
                'description' => "Super Admin menyetujui perubahan jadwal untuk PEMIRA '{$election->name}'",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => [
                    'election_id' => $election->id,
                    'old_registration_start_at' => $req->old_registration_start_at?->toDateTimeString(),
                    'old_registration_end_at' => $req->old_registration_end_at?->toDateTimeString(),
                    'new_registration_start_at' => $req->new_registration_start_at?->toDateTimeString(),
                    'new_registration_end_at' => $req->new_registration_end_at?->toDateTimeString(),
                    'old_voting_start_at' => $req->old_voting_start_at?->toDateTimeString(),
                    'old_voting_end_at' => $req->old_voting_end_at?->toDateTimeString(),
                    'new_voting_start_at' => $req->new_voting_start_at?->toDateTimeString(),
                    'new_voting_end_at' => $req->new_voting_end_at?->toDateTimeString(),
                    'reason' => $req->reason,
                ],
            ]);
        });

        session()->flash('success', 'Pengajuan perubahan jadwal berhasil disetujui.');

        $this->closeApproveModal();
    }

    public function openRejectModal(int $id): void
    {
        Gate::authorize('super-admin-only');

        $request = ScheduleChangeRequest::with('election')->findOrFail($id);

        if (! $request->isPending()) {
            session()->flash('error', 'Pengajuan ini tidak dapat ditolak karena telah diproses sebelumnya.');

            return;
        }

        $this->rejectRequestId = $request->id;
        $this->review_note = '';
        $this->resetValidation();
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->rejectRequestId = null;
        $this->review_note = '';
        $this->resetValidation();
    }

    public function rejectRequest(): void
    {
        Gate::authorize('super-admin-only');

        if (! $this->rejectRequestId) {
            return;
        }

        $validated = $this->validate([
            'review_note' => ['required', 'string', 'min:5', 'max:1000'],
        ], [
            'review_note.required' => 'Alasan penolakan wajib diisi.',
            'review_note.min' => 'Alasan penolakan minimal 5 karakter.',
            'review_note.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        DB::transaction(function () use ($validated) {
            $req = ScheduleChangeRequest::where('id', $this->rejectRequestId)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $req->isPending()) {
                throw ValidationException::withMessages([
                    'general' => 'Pengajuan ini telah diproses sebelumnya dan tidak dapat ditolak kembali.',
                ]);
            }

            $req->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'review_note' => trim($validated['review_note']),
                'reviewed_at' => Carbon::now(),
            ]);

            $election = $req->election;

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'schedule_change_rejected',
                'entity_type' => 'ScheduleChangeRequest',
                'entity_id' => $req->id,
                'description' => "Super Admin menolak perubahan jadwal voting untuk PEMIRA '{$election?->name}'",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => [
                    'election_id' => $req->election_id,
                    'review_note' => $validated['review_note'],
                    'reason' => $req->reason,
                ],
            ]);
        });

        session()->flash('success', 'Pengajuan perubahan jadwal ditolak.');

        $this->closeRejectModal();
    }

    protected function resetForm(): void
    {
        $this->selectedRequestId = null;
        $this->election_id = null;
        $this->new_voting_start_at = '';
        $this->new_voting_end_at = '';
        $this->reason = '';
        $this->current_voting_start_at = null;
        $this->current_voting_end_at = null;
    }

    #[Computed]
    public function statistics(): array
    {
        $base = ScheduleChangeRequest::query();

        if (! Auth::user()->isSuperAdmin()) {
            $base->where('requested_by', Auth::id());
        }

        return [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'approved' => (clone $base)->where('status', 'approved')->count(),
            'rejected' => (clone $base)->where('status', 'rejected')->count(),
        ];
    }

    #[Computed]
    public function availableElections(): Collection
    {
        return Election::query()->orderBy('year', 'desc')->orderBy('id', 'desc')->get();
    }

    #[Computed]
    public function requests()
    {
        $query = ScheduleChangeRequest::query()
            ->with(['election', 'requester', 'reviewer'])
            ->latest('id');

        if (! Auth::user()->isSuperAdmin()) {
            $query->where('requested_by', Auth::id());
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->electionFilter) {
            $query->where('election_id', $this->electionFilter);
        }

        if ($this->search) {
            $term = '%'.trim($this->search).'%';
            $query->where(function (Builder $q) use ($term) {
                $q->where('reason', 'like', $term)
                    ->orWhere('review_note', 'like', $term)
                    ->orWhereHas('election', fn ($eq) => $eq->where('name', 'like', $term))
                    ->orWhereHas('requester', fn ($uq) => $uq->where('email', 'like', $term));
            });
        }

        return $query->paginate($this->perPage);
    }

    public function render(): View
    {
        return view('livewire.admin.schedule-requests.index', [
            'items' => $this->requests,
            'stats' => $this->statistics,
            'elections' => $this->availableElections,
            'isSuperAdmin' => Auth::user()?->isSuperAdmin() ?? false,
        ]);
    }
}
