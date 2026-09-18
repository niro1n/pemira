<?php

namespace App\Livewire\Auth;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Atur Ulang Kata Sandi — PEMIRA 2026')]
class ResetPassword extends Component
{
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request()->query('email', '');
    }

    protected function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    protected function messages(): array
    {
        return [
            'token.required' => 'Token reset kata sandi tidak ditemukan.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ];
    }

    public function resetPassword()
    {
        $this->validate();

        $status = Password::reset(
            [
                'token' => $this->token,
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'password_reset_completed',
                    'entity_type' => 'User',
                    'entity_id' => (string) $user->id,
                    'description' => 'Kata sandi berhasil diatur ulang',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            session()->flash('status', 'Kata sandi Anda berhasil diperbarui. Silakan masuk menggunakan kata sandi baru.');

            return redirect()->route('login');
        }

        $this->addError('email', match ($status) {
            Password::INVALID_USER => 'Pengguna dengan alamat email ini tidak ditemukan.',
            Password::INVALID_TOKEN => 'Token reset kata sandi tidak valid atau telah kedaluwarsa.',
            Password::RESET_THROTTLED => 'Terlalu banyak percobaan. Silakan coba lagi beberapa saat.',
            default => 'Gagal mengatur ulang kata sandi. Silakan coba lagi.',
        });
    }

    public function render()
    {
        return view('pages.auth.reset-password');
    }
}
