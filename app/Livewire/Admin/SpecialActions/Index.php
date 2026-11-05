<?php

namespace App\Livewire\Admin\SpecialActions;

use App\Models\AuditLog;
use App\Models\EligibleVoter;
use App\Models\RegistrationOtp;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\VoterAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    public bool $isRealtimeResultsEnabled = true;

    public bool $showConfirmModal = false;

    public bool $targetState = false;

    public string $otpEmail = '';

    public string $manualOtpCode = '';

    public ?string $generatedOtpResult = null;

    public ?string $generatedOtpEmail = null;

    public ?string $generatedOtpExpiresAt = null;

    public bool $showOtpSuccessModal = false;

    public string $voterSearch = '';

    public ?int $selectedEligibleVoterId = null;

    public ?EligibleVoter $selectedVoter = null;

    public string $activationEmail = '';

    public string $activationPassword = '';

    public string $activationNote = '';

    public bool $showActivationModal = false;

    public bool $showActivationSuccessModal = false;

    public ?array $createdAccountData = null;

    public function mount(): void
    {
        Gate::authorize('super-admin-only');
        $this->isMaintenanceMode = SystemSetting::isMaintenanceMode();
        $this->isRealtimeResultsEnabled = SystemSetting::isRealtimeResultsEnabled();
    }

    public function toggleRealtimeResults(bool $enabled): void
    {
        Gate::authorize('super-admin-only');

        SystemSetting::setRealtimeResultsEnabled($enabled);
        $this->isRealtimeResultsEnabled = $enabled;

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $enabled ? 'realtime_results_enabled' : 'realtime_results_disabled',
            'entity_type' => 'SystemSetting',
            'entity_id' => null,
            'description' => $enabled
                ? 'Super Admin mengaktifkan tampilan hasil perhitungan suara real-time untuk pemilih'
                : 'Super Admin menonaktifkan tampilan hasil perhitungan suara real-time untuk pemilih',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'show_realtime_results_to_voters' => $enabled,
                'actor_email' => Auth::user()?->email,
            ],
        ]);

        session()->flash('success', $enabled
            ? 'Tampilan hasil perhitungan suara real-time untuk pemilih berhasil diaktifkan.'
            : 'Tampilan hasil perhitungan suara real-time untuk pemilih berhasil dinonaktifkan.');
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
        return $this->recentMaintenanceLogs->first();
    }

    #[Computed]
    public function recentOtpRequests(): Collection
    {
        return RegistrationOtp::query()
            ->latest('updated_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function searchedVoters(): Collection
    {
        $q = trim($this->voterSearch);
        if (strlen($q) < 2) {
            return new Collection();
        }

        return EligibleVoter::query()
            ->with(['studyProgram', 'voterAccount.user'])
            ->where(function ($query) use ($q) {
                $query->where('nim', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%");
            })
            ->limit(6)
            ->get();
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


    public function generateManualOtp(): void
    {
        Gate::authorize('super-admin-only');

        $this->validate([
            'otpEmail' => ['required', 'string', 'email', 'max:255'],
            'manualOtpCode' => ['nullable', 'string', 'digits:6'],
        ], [
            'otpEmail.required' => 'Alamat email mahasiswa wajib diisi.',
            'otpEmail.email' => 'Format alamat email tidak valid.',
            'manualOtpCode.digits' => 'Kode OTP kustom harus berupa 6 digit angka.',
        ]);

        $email = trim(strtolower($this->otpEmail));
        $code = ! empty($this->manualOtpCode) ? trim($this->manualOtpCode) : (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(60);

        RegistrationOtp::updateOrCreate(
            ['email' => $email],
            [
                'otp_hash' => hash('sha256', $code),
                'expires_at' => $expiresAt,
                'attempts' => 0,
                'resend_available_at' => now(),
                'used_at' => null,
            ]
        );

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'superadmin_generated_registration_otp',
            'entity_type' => 'RegistrationOtp',
            'entity_id' => null,
            'description' => "Super Admin men-generate kode OTP manual ({$code}) untuk email {$email}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => [
                'email' => $email,
                'otp_code' => $code,
                'actor_email' => Auth::user()?->email,
                'expires_at' => $expiresAt->toIso8601String(),
            ],
        ]);

        $this->generatedOtpResult = $code;
        $this->generatedOtpEmail = $email;
        $this->generatedOtpExpiresAt = $expiresAt->timezone('Asia/Makassar')->translatedFormat('d M Y, H:i').' WITA';
        $this->showOtpSuccessModal = true;
        $this->manualOtpCode = '';

        session()->flash('otp_success', "Kode OTP manual untuk {$email} berhasil dibuat: {$code}");
    }

    public function quickGenerateOtp(string $email): void
    {
        $this->otpEmail = $email;
        $this->manualOtpCode = '';
        $this->generateManualOtp();
    }

    public function closeOtpSuccessModal(): void
    {
        $this->showOtpSuccessModal = false;
    }


    public function updatedVoterSearch(): void
    {
        $this->resetErrorBag(['voterSearch', 'activation']);
    }

    public function selectVoterForActivation(int $voterId): void
    {
        Gate::authorize('super-admin-only');

        $voter = EligibleVoter::with(['studyProgram', 'voterAccount.user'])->findOrFail($voterId);

        if ($voter->voterAccount !== null) {
            $existingEmail = $voter->voterAccount->user?->email ?? '-';
            $this->addError('voterSearch', "Mahasiswa {$voter->name} (NIM: {$voter->nim}) sudah memiliki akun terdaftar dengan email: {$existingEmail}");

            return;
        }

        $this->selectedVoter = $voter;
        $this->selectedEligibleVoterId = $voter->id;
        $this->activationEmail = '';
        $this->activationPassword = 'Pemira'.$voter->nim.'!';
        $this->activationNote = 'Verifikasi manual Super Admin karena kendala pengiriman OTP email mahasiswa';
        $this->showActivationModal = true;
    }

    public function cancelActivation(): void
    {
        $this->showActivationModal = false;
        $this->selectedVoter = null;
        $this->selectedEligibleVoterId = null;
    }

    public function activateVoterAccount(): void
    {
        Gate::authorize('super-admin-only');

        if (! $this->selectedEligibleVoterId) {
            $this->addError('activation', 'Data mahasiswa belum dipilih.');

            return;
        }

        $voter = EligibleVoter::with(['studyProgram', 'voterAccount'])->findOrFail($this->selectedEligibleVoterId);

        $this->validate([
            'activationEmail' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'activationPassword' => ['required', 'string', 'min:8'],
            'activationNote' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'activationEmail.required' => 'Email mahasiswa wajib diisi.',
            'activationEmail.email' => 'Format email tidak valid.',
            'activationEmail.unique' => 'Email ini sudah terdaftar pada pengguna lain di sistem.',
            'activationPassword.required' => 'Kata sandi wajib diisi.',
            'activationPassword.min' => 'Kata sandi minimal 8 karakter.',
            'activationNote.required' => 'Catatan alasan aktivasi manual wajib diisi.',
            'activationNote.min' => 'Catatan verifikasi minimal 5 karakter.',
        ]);

        if ($voter->voterAccount !== null) {
            $this->addError('activationEmail', 'Mahasiswa ini sudah memiliki akun pemilih terdaftar.');

            return;
        }

        $email = trim(strtolower($this->activationEmail));
        $password = $this->activationPassword;
        $note = $this->activationNote;

        DB::transaction(function () use ($voter, $email, $password, $note) {
            $user = User::create([
                'name' => $voter->name,
                'email' => $email,
                'password' => $password,
                'role' => 'voter',
                'email_verified_at' => now(),
            ]);

            VoterAccount::create([
                'user_id' => $user->id,
                'eligible_voter_id' => $voter->id,
            ]);

            RegistrationOtp::where('email', $email)->update(['used_at' => now()]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'superadmin_manual_voter_activation',
                'entity_type' => 'User',
                'entity_id' => (string) $user->id,
                'description' => "Super Admin mengaktivasi akun voter NIM {$voter->nim} ({$voter->name}) dengan email {$email}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => [
                    'nim' => $voter->nim,
                    'name' => $voter->name,
                    'email' => $email,
                    'study_program' => $voter->studyProgram?->name,
                    'admin_notes' => $note,
                    'actor_email' => Auth::user()?->email,
                ],
            ]);

            $this->createdAccountData = [
                'nim' => $voter->nim,
                'name' => $voter->name,
                'prodi' => $voter->studyProgram?->name ?? 'Jurusan Terdaftar',
                'email' => $email,
                'password' => $password,
                'note' => $note,
                'date' => now()->timezone('Asia/Makassar')->translatedFormat('d M Y, H:i').' WITA',
            ];
        });

        $this->showActivationModal = false;
        $this->showActivationSuccessModal = true;
        $this->voterSearch = '';
        $this->selectedVoter = null;
        $this->selectedEligibleVoterId = null;

        session()->flash('success', "Akun voter atas nama {$voter->name} (NIM: {$voter->nim}) berhasil diaktivasi manual!");
    }

    public function closeActivationSuccessModal(): void
    {
        $this->showActivationSuccessModal = false;
    }

    public function render(): View
    {
        return view('livewire.admin.special-actions.index', [
            'recentLogs' => $this->recentMaintenanceLogs,
            'lastLog' => $this->lastMaintenanceLog,
            'recentOtps' => $this->recentOtpRequests,
            'searchedVoters' => $this->searchedVoters,
        ]);
    }
}
