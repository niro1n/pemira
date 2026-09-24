<?php

namespace App\Livewire\Auth;

use App\Models\AdminInvitation;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Aktivasi Akun Admin — PEMIRA 2026')]
class AcceptInvitation extends Component
{
    public string $token = '';

    public string $email = '';

    public string $name = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $invitationStatus = '';

    public function mount(string $token): void
    {
        $this->token = trim($token);

        $hashedToken = AdminInvitation::hashToken($this->token);
        $invitation = AdminInvitation::where('token', $hashedToken)->first();

        if (! $invitation) {
            $this->invitationStatus = 'not_found';

            return;
        }

        if ($invitation->isRevoked()) {
            $this->invitationStatus = 'revoked';

            return;
        }

        if ($invitation->isAccepted()) {
            $this->invitationStatus = 'already_accepted';

            return;
        }

        if ($invitation->isExpired()) {
            $this->invitationStatus = 'expired';

            return;
        }

        $this->invitationStatus = 'valid';
        $this->email = $invitation->email;
    }

    public function createAccount()
    {
        $throttleKey = 'accept-invitation:'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('general', "Terlalu banyak percobaan. Silakan coba lagi dalam {$seconds} detik.");

            return;
        }

        RateLimiter::hit($throttleKey, 60);

        $hashedToken = AdminInvitation::hashToken($this->token);
        $invitation = AdminInvitation::where('token', $hashedToken)->first();

        if (! $invitation || ! $invitation->isValid()) {
            $this->invitationStatus = $invitation ? $invitation->status()->value : 'not_found';
            $this->addError('general', 'Undangan ini sudah tidak valid atau telah kedaluwarsa.');

            return;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        if (User::where('email', $invitation->email)->exists()) {
            $this->addError('general', 'Alamat email ini sudah terdaftar sebagai akun pengguna.');

            return;
        }

        try {
            DB::transaction(function () use ($invitation, $validated) {
                $user = User::create([
                    'name' => trim($validated['name']),
                    'email' => $invitation->email,
                    'password' => Hash::make($validated['password']),
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]);

                $invitation->update([
                    'accepted_at' => now(),
                ]);

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'admin_invitation_accepted',
                    'entity_type' => 'User',
                    'entity_id' => (string) $user->id,
                    'description' => "Akun admin baru berhasil diaktifkan via undangan email: {$user->email}",
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'metadata' => [
                        'invitation_id' => $invitation->id,
                        'invited_by' => $invitation->invited_by,
                        'email' => $user->email,
                    ],
                ]);

                Auth::login($user);
                if (request()->hasSession()) {
                    request()->session()->regenerate();
                }
            });

            RateLimiter::clear($throttleKey);

            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang! Akun Administrator Anda telah aktif.');
        } catch (\Throwable $e) {
            $this->addError('general', 'Terjadi kesalahan sistem saat membuat akun. Silakan coba kembali.');
        }
    }

    public function render(): View
    {
        return view('pages.auth.accept-invitation');
    }
}
