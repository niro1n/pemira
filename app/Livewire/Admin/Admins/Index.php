<?php

namespace App\Livewire\Admin\Admins;

use App\Mail\AdminInvitationMail;
use App\Models\AdminInvitation;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['heading' => 'MANAJEMEN ADMIN'])]
#[Title('Manajemen Admin — PEMIRA PNB')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'role', history: true)]
    public string $roleFilter = 'all';

    #[Url(as: 'status', history: true)]
    public string $statusFilter = 'all';

    #[Url(as: 'start_date', history: true)]
    public string $startDate = '';

    #[Url(as: 'end_date', history: true)]
    public string $endDate = '';

    public int $perPage = 10;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public bool $showFilterModal = false;

    public bool $showDetailModal = false;

    public bool $showCreateModal = false;

    public bool $showInviteModal = false;

    public bool $showInvitationsModal = false;

    public bool $showRevokeModal = false;

    public bool $showEditModal = false;

    public bool $showDeleteModal = false;

    public bool $showToggleStatusModal = false;

    public bool $showChangeRoleModal = false;

    public string $targetRole = '';

    public string $role = 'admin';

    public string $createRole = 'admin';

    public ?int $selectedAdminId = null;

    public ?int $selectedInvitationId = null;

    public string $email = '';

    public string $inviteEmail = '';

    public string $invitationSearch = '';

    public string $invitationStatusFilter = 'all';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $isActive = true;

    public function mount(): void
    {
        Gate::authorize('super-admin-only');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStartDate(): void
    {
        $this->resetPage();
    }

    public function updatingEndDate(): void
    {
        $this->resetPage();
    }

    public function updatingInvitationSearch(): void
    {
        $this->resetPage(pageName: 'invitations_page');
    }

    public function updatingInvitationStatusFilter(): void
    {
        $this->resetPage(pageName: 'invitations_page');
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    #[Computed]
    public function admins(): LengthAwarePaginator
    {
        $query = User::query()
            ->whereIn('role', ['admin', 'super_admin'])
            ->with(['voterAccount.eligibleVoter']);

        if ($this->search !== '') {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('email', 'like', "%{$term}%")
                    ->orWhereHas('voterAccount.eligibleVoter', function ($sub) use ($term) {
                        $sub->where('name', 'like', "%{$term}%");
                    });
            });
        }

        if ($this->roleFilter !== 'all') {
            $query->where('role', $this->roleFilter);
        }

        if ($this->statusFilter === 'active') {
            $query->whereNotNull('email_verified_at');
        } elseif ($this->statusFilter === 'inactive') {
            $query->whereNull('email_verified_at');
        }

        if ($this->startDate !== '') {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate !== '') {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    #[Computed]
    public function statistics(): array
    {
        $baseQuery = User::query()->whereIn('role', ['admin', 'super_admin']);

        return [
            'total' => (clone $baseQuery)->count(),
            'admin_count' => (clone $baseQuery)->where('role', 'admin')->count(),
            'super_admin_count' => (clone $baseQuery)->where('role', 'super_admin')->count(),
            'active_count' => (clone $baseQuery)->whereNotNull('email_verified_at')->count(),
            'inactive_count' => (clone $baseQuery)->whereNull('email_verified_at')->count(),
            'pending_invitations_count' => AdminInvitation::query()->pending()->count(),
        ];
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        $count = 0;

        if ($this->roleFilter !== 'all') {
            $count++;
        }

        if ($this->statusFilter !== 'all') {
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
    public function selectedAdmin(): ?User
    {
        if (! $this->selectedAdminId) {
            return null;
        }

        return User::query()
            ->with(['voterAccount.eligibleVoter'])
            ->find($this->selectedAdminId);
    }

    #[Computed]
    public function recentAuditLogs(): array
    {
        if (! $this->selectedAdminId) {
            return [];
        }

        return AuditLog::query()
            ->where('user_id', $this->selectedAdminId)
            ->latest('id')
            ->limit(5)
            ->get()
            ->all();
    }

    #[Computed]
    public function adminInvitations(): LengthAwarePaginator
    {
        $query = AdminInvitation::query()
            ->with(['inviter']);

        if ($this->invitationSearch !== '') {
            $term = trim($this->invitationSearch);
            $query->where('email', 'like', "%{$term}%");
        }

        if ($this->invitationStatusFilter === 'pending') {
            $query->pending();
        } elseif ($this->invitationStatusFilter === 'accepted') {
            $query->accepted();
        } elseif ($this->invitationStatusFilter === 'expired') {
            $query->expired();
        } elseif ($this->invitationStatusFilter === 'revoked') {
            $query->revoked();
        }

        return $query->latest('id')->paginate(10, ['*'], 'invitations_page');
    }

    #[Computed]
    public function selectedInvitation(): ?AdminInvitation
    {
        if (! $this->selectedInvitationId) {
            return null;
        }

        return AdminInvitation::find($this->selectedInvitationId);
    }

    public function openInvitationsModal(): void
    {
        $this->showInvitationsModal = true;
    }

    public function closeInvitationsModal(): void
    {
        $this->showInvitationsModal = false;
    }

    public function openInviteModal(): void
    {
        $this->resetErrorBag();
        $this->inviteEmail = '';
        $this->showInviteModal = true;
    }

    public function closeInviteModal(): void
    {
        $this->showInviteModal = false;
        $this->inviteEmail = '';
        $this->resetErrorBag();
    }

    public function sendInvitation(): void
    {
        Gate::authorize('super-admin-only');

        $this->validate([
            'inviteEmail' => ['required', 'string', 'email', 'max:255'],
        ], [
            'inviteEmail.required' => 'Alamat email calon admin wajib diisi.',
            'inviteEmail.email' => 'Format email tidak valid.',
        ]);

        $email = strtolower(trim($this->inviteEmail));

        if (User::where('email', $email)->exists()) {
            $this->addError('inviteEmail', 'Email ini sudah terdaftar sebagai pengguna dalam sistem.');

            return;
        }

        $existingPending = AdminInvitation::where('email', $email)
            ->pending()
            ->first();

        if ($existingPending) {
            $this->addError('inviteEmail', 'Undangan untuk email ini sudah pernah dikirim dan masih aktif (pending). Silakan gunakan opsi Kirim Ulang pada tabel undangan.');

            return;
        }

        $plainToken = AdminInvitation::generatePlainToken();
        $hashedToken = AdminInvitation::hashToken($plainToken);

        $invitation = AdminInvitation::create([
            'email' => $email,
            'token' => $hashedToken,
            'expires_at' => now()->addHours(24),
            'invited_by' => Auth::id(),
        ]);

        RateLimiter::hit('resend-admin-invitation:'.$invitation->id, 60);

        Mail::to($email)->send(new AdminInvitationMail($invitation, $plainToken));

        $this->dispatch('invitation-cooldown-started', id: $invitation->id, seconds: 60);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'admin_invitation_sent',
            'entity_type' => 'AdminInvitation',
            'entity_id' => $invitation->id,
            'description' => "Super Admin mengundang calon admin: {$email}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'email' => $email,
                'expires_at' => $invitation->expires_at->toIso8601String(),
            ],
        ]);

        session()->flash('success', "Undangan pendaftaran admin berhasil dikirim ke {$email}. Calon admin memiliki waktu 24 jam untuk melengkapi akun.");
        $this->closeInviteModal();
    }

    public function resendInvitation(int $invitationId): void
    {
        Gate::authorize('super-admin-only');

        $invitation = AdminInvitation::find($invitationId);

        if (! $invitation || $invitation->isAccepted()) {
            session()->flash('error', 'Undangan tidak ditemukan atau sudah diterima.');

            return;
        }

        $rateLimitKey = 'resend-admin-invitation:'.$invitation->id;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            session()->flash('error', "Harap tunggu {$seconds} detik sebelum mengirim ulang undangan ke {$invitation->email}.");

            return;
        }
        RateLimiter::hit($rateLimitKey, 60);

        $plainToken = AdminInvitation::generatePlainToken();
        $hashedToken = AdminInvitation::hashToken($plainToken);

        $invitation->update([
            'token' => $hashedToken,
            'expires_at' => now()->addHours(24),
            'revoked_at' => null,
        ]);

        Mail::to($invitation->email)->send(new AdminInvitationMail($invitation, $plainToken));

        $this->dispatch('invitation-cooldown-started', id: $invitation->id, seconds: 60);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'admin_invitation_resent',
            'entity_type' => 'AdminInvitation',
            'entity_id' => $invitation->id,
            'description' => "Super Admin mengirim ulang undangan admin ke: {$invitation->email}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'email' => $invitation->email,
                'expires_at' => $invitation->expires_at->toIso8601String(),
            ],
        ]);

        session()->flash('success', "Undangan ke {$invitation->email} berhasil dikirim ulang.");
    }

    public function getInvitationCooldownSeconds(int $invitationId): int
    {
        $rateLimitKey = 'resend-admin-invitation:'.$invitationId;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            return RateLimiter::availableIn($rateLimitKey);
        }

        return 0;
    }

    public function openRevokeModal(int $id): void
    {
        Gate::authorize('super-admin-only');

        $invitation = AdminInvitation::find($id);

        if (! $invitation || ! $invitation->isPending()) {
            session()->flash('error', 'Hanya undangan dengan status PENDING yang dapat dibatalkan.');

            return;
        }

        $this->selectedInvitationId = $id;
        $this->showRevokeModal = true;
    }

    public function closeRevokeModal(): void
    {
        $this->selectedInvitationId = null;
        $this->showRevokeModal = false;
    }

    public function revokeInvitation(): void
    {
        Gate::authorize('super-admin-only');

        if (! $this->selectedInvitationId) {
            return;
        }

        $invitation = AdminInvitation::find($this->selectedInvitationId);

        if (! $invitation || ! $invitation->isPending()) {
            session()->flash('error', 'Hanya undangan dengan status PENDING yang dapat dibatalkan.');
            $this->closeRevokeModal();

            return;
        }

        $invitation->update([
            'revoked_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'admin_invitation_revoked',
            'entity_type' => 'AdminInvitation',
            'entity_id' => $invitation->id,
            'description' => "Super Admin membatalkan undangan admin untuk: {$invitation->email}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'email' => $invitation->email,
            ],
        ]);

        session()->flash('success', "Undangan untuk {$invitation->email} berhasil dibatalkan.");
        $this->closeRevokeModal();
    }

    public function openCreateModal(): void
    {
        $this->resetErrorBag();
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->createRole = 'admin';
        $this->isActive = true;
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->createRole = 'admin';
        $this->resetErrorBag();
    }

    public function createAdmin(): void
    {
        Gate::authorize('super-admin-only');

        $validated = $this->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            'createRole' => ['required', Rule::in(['admin', 'super_admin'])],
            'isActive' => ['boolean'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'createRole.required' => 'Peran akun wajib dipilih.',
            'createRole.in' => 'Pilihan peran akun tidak valid.',
        ]);

        $admin = User::create([
            'email' => strtolower(trim($validated['email'])),
            'password' => Hash::make($validated['password']),
            'role' => $validated['createRole'],
            'email_verified_at' => $this->isActive ? now() : null,
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'admin_created',
            'entity_type' => 'User',
            'entity_id' => $admin->id,
            'description' => "Super Admin membuat akun admin baru: {$admin->email} ({$admin->role})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'email' => $admin->email,
                'role' => $admin->role,
                'status' => $this->isActive ? 'active' : 'inactive',
            ],
        ]);

        session()->flash('success', "Akun admin {$admin->email} berhasil ditambahkan.");
        $this->closeCreateModal();
    }

    public function openEditModal(int $id): void
    {
        $target = User::find($id);

        if (! $target || ! in_array($target->role, ['admin', 'super_admin'], true)) {
            return;
        }

        $this->resetErrorBag();
        $this->selectedAdminId = $id;
        $this->email = $target->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = $target->role;
        $this->isActive = $target->email_verified_at !== null;
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->selectedAdminId = null;
        $this->showEditModal = false;
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = 'admin';
        $this->resetErrorBag();
    }

    public function updateAdmin(): void
    {
        Gate::authorize('super-admin-only');

        if (! $this->selectedAdminId) {
            return;
        }

        $target = User::find($this->selectedAdminId);

        if (! $target || ! in_array($target->role, ['admin', 'super_admin'], true)) {
            $this->closeEditModal();

            return;
        }

        $validated = $this->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($target->id)],
            'password' => ['nullable', 'string', Password::defaults(), 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'super_admin'])],
            'isActive' => ['boolean'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'role.required' => 'Peran akun wajib dipilih.',
            'role.in' => 'Pilihan peran akun tidak valid.',
        ]);

        if ($target->id === Auth::id() && $validated['role'] !== $target->role) {
            $this->addError('role', 'Anda tidak dapat mengubah peran akun Anda sendiri.');

            return;
        }

        if ($target->id === Auth::id() && ! $this->isActive) {
            $this->addError('isActive', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');

            return;
        }

        if ($target->isSuperAdmin() && $validated['role'] === 'admin') {
            $activeSuperAdminCount = User::where('role', 'super_admin')
                ->whereNotNull('email_verified_at')
                ->count();

            if ($activeSuperAdminCount <= 1) {
                $this->addError('role', 'Akun Super Admin aktif terakhir tidak dapat diturunkan perannya.');

                return;
            }
        }

        if ($target->isSuperAdmin() && ! $this->isActive) {
            $activeSuperAdminCount = User::where('role', 'super_admin')
                ->whereNotNull('email_verified_at')
                ->count();

            if ($activeSuperAdminCount <= 1) {
                $this->addError('isActive', 'Akun Super Admin aktif terakhir tidak dapat dinonaktifkan.');

                return;
            }
        }

        $oldEmail = $target->email;
        $oldRole = $target->role;
        $target->email = strtolower(trim($validated['email']));
        $target->role = $validated['role'];

        if (! empty($validated['password'])) {
            $target->password = Hash::make($validated['password']);
        }

        $target->email_verified_at = $this->isActive
            ? ($target->email_verified_at ?? now())
            : null;

        $target->save();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'admin_updated',
            'entity_type' => 'User',
            'entity_id' => $target->id,
            'description' => "Super Admin memperbarui data akun admin: {$target->email}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'old_email' => $oldEmail,
                'new_email' => $target->email,
                'status' => $this->isActive ? 'active' : 'inactive',
                'password_changed' => ! empty($validated['password']),
                'role_changed' => $oldRole !== $target->role,
                'old_role' => $oldRole,
                'new_role' => $target->role,
            ],
        ]);

        session()->flash('success', "Data akun admin {$target->email} berhasil diperbarui.");
        $this->closeEditModal();
    }

    public function openToggleStatusModal(int $id): void
    {
        $target = User::find($id);

        if (! $target || ! in_array($target->role, ['admin', 'super_admin'], true)) {
            return;
        }

        if ($target->id === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat mengubah status aktif akun Anda sendiri.');

            return;
        }

        if ($target->isSuperAdmin() && $target->email_verified_at !== null) {
            $activeSuperAdminCount = User::where('role', 'super_admin')
                ->whereNotNull('email_verified_at')
                ->count();

            if ($activeSuperAdminCount <= 1) {
                session()->flash('error', 'Akun Super Admin aktif terakhir tidak dapat dinonaktifkan.');

                return;
            }
        }

        $this->selectedAdminId = $id;
        $this->showToggleStatusModal = true;
    }

    public function closeToggleStatusModal(): void
    {
        $this->selectedAdminId = null;
        $this->showToggleStatusModal = false;
    }

    public function toggleStatus(): void
    {
        Gate::authorize('super-admin-only');

        if (! $this->selectedAdminId) {
            return;
        }

        $target = User::find($this->selectedAdminId);

        if (! $target || ! in_array($target->role, ['admin', 'super_admin'], true)) {
            $this->closeToggleStatusModal();

            return;
        }

        if ($target->id === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat mengubah status akun Anda sendiri.');
            $this->closeToggleStatusModal();

            return;
        }

        $willBeActive = $target->email_verified_at === null;

        if (! $willBeActive && $target->isSuperAdmin()) {
            $activeSuperAdminCount = User::where('role', 'super_admin')
                ->whereNotNull('email_verified_at')
                ->count();

            if ($activeSuperAdminCount <= 1) {
                session()->flash('error', 'Akun Super Admin aktif terakhir tidak dapat dinonaktifkan.');
                $this->closeToggleStatusModal();

                return;
            }
        }

        $target->email_verified_at = $willBeActive ? now() : null;
        $target->save();

        $statusLabel = $willBeActive ? 'diaktifkan' : 'dinonaktifkan';

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'admin_status_changed',
            'entity_type' => 'User',
            'entity_id' => $target->id,
            'description' => "Super Admin mengubah status admin {$target->email} menjadi {$statusLabel}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'target_user_id' => $target->id,
                'target_email' => $target->email,
                'new_status' => $willBeActive ? 'active' : 'inactive',
            ],
        ]);

        session()->flash('success', "Akun admin {$target->email} berhasil {$statusLabel}.");
        $this->closeToggleStatusModal();
    }

    public function openChangeRoleModal(int $id): void
    {
        Gate::authorize('super-admin-only');

        $target = User::find($id);

        if (! $target || ! in_array($target->role, ['admin', 'super_admin'], true)) {
            return;
        }

        if ($target->id === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat mengubah peran akun Anda sendiri.');

            return;
        }

        if ($target->isSuperAdmin()) {
            $activeSuperAdminCount = User::where('role', 'super_admin')
                ->whereNotNull('email_verified_at')
                ->count();

            if ($activeSuperAdminCount <= 1) {
                session()->flash('error', 'Akun Super Admin aktif terakhir tidak dapat diturunkan perannya.');

                return;
            }
        }

        $this->selectedAdminId = $id;
        $this->targetRole = $target->role === 'super_admin' ? 'admin' : 'super_admin';
        $this->showChangeRoleModal = true;
    }

    public function closeChangeRoleModal(): void
    {
        $this->selectedAdminId = null;
        $this->showChangeRoleModal = false;
        $this->targetRole = '';
    }

    public function changeRole(): void
    {
        Gate::authorize('super-admin-only');

        if (! $this->selectedAdminId) {
            return;
        }

        $target = User::find($this->selectedAdminId);

        if (! $target || ! in_array($target->role, ['admin', 'super_admin'], true)) {
            $this->closeChangeRoleModal();

            return;
        }

        if ($target->id === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat mengubah peran akun Anda sendiri.');
            $this->closeChangeRoleModal();

            return;
        }

        $newRole = $target->role === 'super_admin' ? 'admin' : 'super_admin';

        if ($target->isSuperAdmin()) {
            $activeSuperAdminCount = User::where('role', 'super_admin')
                ->whereNotNull('email_verified_at')
                ->count();

            if ($activeSuperAdminCount <= 1) {
                session()->flash('error', 'Akun Super Admin aktif terakhir tidak dapat diturunkan perannya.');
                $this->closeChangeRoleModal();

                return;
            }
        }

        $oldRole = $target->role;
        $target->role = $newRole;
        $target->save();

        $isPromote = $newRole === 'super_admin';
        $actionName = $isPromote ? 'admin_promoted' : 'admin_demoted';
        $roleTitle = $isPromote ? 'Super Administrator' : 'Admin KPR';
        $actionDescription = $isPromote
            ? "Super Admin menaikkan peran {$target->email} menjadi Super Administrator"
            : "Super Admin menurunkan peran {$target->email} menjadi Admin KPR";

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $actionName,
            'entity_type' => 'User',
            'entity_id' => $target->id,
            'description' => $actionDescription,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'target_user_id' => $target->id,
                'target_email' => $target->email,
                'old_role' => $oldRole,
                'new_role' => $newRole,
            ],
        ]);

        session()->flash('success', "Peran akun {$target->email} berhasil diubah menjadi {$roleTitle}.");
        $this->closeChangeRoleModal();
    }

    public function openDeleteModal(int $id): void
    {
        Gate::authorize('super-admin-only');

        $target = User::find($id);

        if (! $target || ! in_array($target->role, ['admin', 'super_admin'], true)) {
            return;
        }

        if ($target->id === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');

            return;
        }

        if ($target->isSuperAdmin()) {
            session()->flash('error', 'Akun Super Admin tidak dapat dihapus melalui halaman Manajemen Admin.');

            return;
        }

        $this->selectedAdminId = $id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->selectedAdminId = null;
        $this->showDeleteModal = false;
    }

    public function deleteAdmin(): void
    {
        Gate::authorize('super-admin-only');

        if (! $this->selectedAdminId) {
            return;
        }

        $target = User::find($this->selectedAdminId);

        if (! $target || ! in_array($target->role, ['admin', 'super_admin'], true)) {
            $this->closeDeleteModal();

            return;
        }

        if ($target->id === Auth::id()) {
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            $this->closeDeleteModal();

            return;
        }

        if ($target->isSuperAdmin()) {
            session()->flash('error', 'Akun Super Admin tidak dapat dihapus melalui halaman Manajemen Admin.');
            $this->closeDeleteModal();

            return;
        }

        $deletedEmail = $target->email;
        $targetId = $target->id;

        $target->delete();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'admin_deleted',
            'entity_type' => 'User',
            'entity_id' => $targetId,
            'description' => "Super Admin menghapus akun admin: {$deletedEmail}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'deleted_user_id' => $targetId,
                'deleted_email' => $deletedEmail,
            ],
        ]);

        session()->flash('success', "Akun admin {$deletedEmail} berhasil dihapus.");
        $this->closeDeleteModal();
    }

    public function openDetailModal(int $id): void
    {
        $target = User::find($id);

        if (! $target || ! in_array($target->role, ['admin', 'super_admin'], true)) {
            return;
        }

        $this->selectedAdminId = $id;
        $this->showDetailModal = true;
    }

    public function closeDetailModal(): void
    {
        $this->selectedAdminId = null;
        $this->showDetailModal = false;
    }

    public function openFilterModal(): void
    {
        $this->showFilterModal = true;
    }

    public function closeFilterModal(): void
    {
        $this->showFilterModal = false;
    }

    public function resetFilters(): void
    {
        $this->roleFilter = 'all';
        $this->statusFilter = 'all';
        $this->startDate = '';
        $this->endDate = '';
        $this->resetPage();
        $this->closeFilterModal();
    }

    public function clearFilter(string $key): void
    {
        match ($key) {
            'role' => $this->roleFilter = 'all',
            'status' => $this->statusFilter = 'all',
            'date' => $this->startDate = $this->endDate = '',
            default => null,
        };

        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.admin.admins.index', [
            'admins' => $this->admins,
            'adminInvitations' => $this->adminInvitations,
            'statistics' => $this->statistics,
            'activeFilterCount' => $this->activeFilterCount,
            'selectedAdmin' => $this->selectedAdmin,
            'selectedInvitation' => $this->selectedInvitation,
            'recentAuditLogs' => $this->recentAuditLogs,
        ]);
    }
}
