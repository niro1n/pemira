<?php

namespace App\Livewire\Auth;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Lupa Kata Sandi — PEMIRA 2026')]
class ForgotPassword extends Component
{
    public string $email = '';

    public ?string $statusMessage = null;

    protected function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ];
    }

    public function sendResetLink(): void
    {
        $this->validate();

        $throttleKey = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', 'Terlalu banyak permintaan reset kata sandi. Silakan coba lagi dalam '.$seconds.' detik.');

            return;
        }

        RateLimiter::hit($throttleKey, 300);

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_THROTTLED) {
            $this->addError('email', 'Harap tunggu sebelum meminta tautan reset kata sandi baru.');

            return;
        }

        $user = User::where('email', $this->email)->first();
        if ($user) {
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'forgot_password_requested',
                'entity_type' => 'User',
                'entity_id' => (string) $user->id,
                'description' => 'Permintaan tautan reset kata sandi diajukan',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        $this->statusMessage = 'Jika alamat email tersebut terdaftar di sistem kami, kami telah mengirimkan instruksi dan tautan untuk mengatur ulang kata sandi.';
        $this->email = '';
    }

    public function render()
    {
        return view('pages.auth.forgot-password');
    }
}
