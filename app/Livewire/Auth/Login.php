<?php

namespace App\Livewire\Auth;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Masuk — PEMIRA 2026')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    protected function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    protected function messages(): array
    {
        return [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }

    public function login()
    {
        $this->validate();

        $throttleKey = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('email', 'Terlalu banyak percobaan masuk. Silakan coba lagi dalam '.$seconds.' detik.');

            return;
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($throttleKey, 300);
            $this->addError('email', 'Kredensial yang dimasukkan tidak cocok dengan data kami.');

            return;
        }

        if (! Auth::user()->hasVerifiedEmail()) {
            Auth::logout();
            $this->addError('email', 'Email Anda belum diverifikasi. Silakan lakukan registrasi atau verifikasi OTP.');

            return;
        }

        RateLimiter::clear($throttleKey);
        session()->regenerate();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'login',
            'entity_type' => 'User',
            'entity_id' => (string) Auth::id(),
            'description' => 'Pengguna berhasil masuk',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $user = Auth::user();
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return redirect()->intended('/admin');
        }

        return redirect()->intended(route('home'));
    }

    public function render()
    {
        return view('pages.auth.login');
    }
}
