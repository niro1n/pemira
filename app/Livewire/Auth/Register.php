<?php

namespace App\Livewire\Auth;

use App\Mail\RegistrationOtpMail;
use App\Models\AuditLog;
use App\Models\EligibleVoter;
use App\Models\RegistrationOtp;
use App\Models\User;
use App\Models\VoterAccount;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.auth')]
#[Title('Daftar Pemilih — PEMIRA 2026')]
class Register extends Component
{
    public int $currentStep = 1;

    public string $nim = '';

    public string $birth_date = '';

    public ?int $eligibleVoterId = null;

    public string $studentName = '';

    public string $studentNim = '';

    public string $studentProdi = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public array $otp = ['', '', '', '', '', ''];

    public function validateStudent()
    {
        $this->validate([
            'nim' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
        ], [
            'nim.required' => 'Nomor Induk Mahasiswa (NIM) wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
        ]);

        $voter = EligibleVoter::where('nim', $this->nim)
            ->whereDate('date_of_birth', $this->birth_date)
            ->first();

        if (! $voter) {
            $this->addError('nim', 'NIM atau tanggal lahir tidak cocok dengan data pemilih aktif.');

            return;
        }

        if (! $voter->is_eligible) {
            $this->addError('nim', 'Status mahasiswa tidak memenuhi syarat sebagai pemilih aktif PEMIRA.');

            return;
        }

        if (VoterAccount::where('eligible_voter_id', $voter->id)->exists()) {
            $this->addError('nim', 'Mahasiswa dengan NIM ini sudah memiliki akun pemilih terdaftar.');

            return;
        }

        $this->eligibleVoterId = $voter->id;
        $this->studentName = $voter->name ?? 'Mahasiswa';
        $this->studentNim = $voter->nim;
        $this->studentProdi = $voter->studyProgram?->name ?? 'Program Studi Terdaftar';
        $this->currentStep = 2;
    }

    public function submitAccountData()
    {
        if (! $this->eligibleVoterId) {
            $this->currentStep = 1;

            return;
        }

        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Alamat email aktif wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar. Silakan masuk atau gunakan email lain.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $otpCode = (string) random_int(100000, 999999);

        RegistrationOtp::updateOrCreate(
            ['email' => $this->email],
            [
                'otp_hash' => hash('sha256', $otpCode),
                'expires_at' => now()->addMinutes(5),
                'attempts' => 0,
                'resend_available_at' => now()->addSeconds(60),
                'used_at' => null,
            ]
        );

        Mail::to($this->email)->send(new RegistrationOtpMail($otpCode, $this->studentName));

        $this->otp = ['', '', '', '', '', ''];
        $this->currentStep = 3;
        $this->dispatch('otp-sent');
    }

    public function verifyOtp()
    {
        if (! $this->eligibleVoterId || ! $this->email) {
            $this->currentStep = 1;

            return;
        }

        $code = implode('', $this->otp);

        if (strlen($code) !== 6 || ! ctype_digit($code)) {
            $this->addError('otp', 'Masukkan 6 digit kode verifikasi OTP yang valid.');

            return;
        }

        $otpRecord = RegistrationOtp::where('email', $this->email)
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (! $otpRecord) {
            $this->addError('otp', 'Kode verifikasi tidak ditemukan. Silakan minta kode baru.');

            return;
        }

        if ($otpRecord->isExpired()) {
            $this->addError('otp', 'Kode OTP sudah kedaluwarsa (berlaku 5 menit). Silakan minta kode baru.');

            return;
        }

        if ($otpRecord->hasExceededAttempts()) {
            $this->addError('otp', 'Batas percobaan verifikasi telah terlampaui. Silakan kirim ulang kode baru.');

            return;
        }

        if (! $otpRecord->verify($code)) {
            $otpRecord->increment('attempts');
            $remaining = 5 - $otpRecord->attempts;
            $this->addError('otp', 'Kode OTP yang dimasukkan salah. Sisa percobaan: '.max(0, $remaining));

            return;
        }

        try {
            DB::transaction(function () use ($otpRecord) {
                if (VoterAccount::where('eligible_voter_id', $this->eligibleVoterId)->exists()) {
                    throw new Exception('Data mahasiswa ini sudah memiliki akun terdaftar.');
                }

                if (User::where('email', $this->email)->exists()) {
                    throw new Exception('Alamat email ini sudah terdaftar.');
                }

                $user = User::create([
                    'email' => $this->email,
                    'password' => $this->password,
                    'role' => 'voter',
                    'email_verified_at' => now(),
                ]);

                VoterAccount::create([
                    'user_id' => $user->id,
                    'eligible_voter_id' => $this->eligibleVoterId,
                ]);

                $otpRecord->update(['used_at' => now()]);

                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'register',
                    'entity_type' => 'User',
                    'entity_id' => (string) $user->id,
                    'description' => 'Pendaftaran akun pemilih berhasil diverifikasi',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            });

            $this->password = '';
            $this->password_confirmation = '';
            $this->currentStep = 4;
        } catch (Exception $e) {
            $this->addError('otp', $e->getMessage());
        }
    }

    public function resendOtp()
    {
        if (! $this->email) {
            return;
        }

        $otpRecord = RegistrationOtp::where('email', $this->email)
            ->latest()
            ->first();

        if ($otpRecord && ! $otpRecord->canResend()) {
            $this->addError('otp', 'Harap tunggu sebelum meminta kode verifikasi baru.');

            return;
        }

        $otpCode = (string) random_int(100000, 999999);

        RegistrationOtp::updateOrCreate(
            ['email' => $this->email],
            [
                'otp_hash' => hash('sha256', $otpCode),
                'expires_at' => now()->addMinutes(5),
                'attempts' => 0,
                'resend_available_at' => now()->addSeconds(60),
                'used_at' => null,
            ]
        );

        Mail::to($this->email)->send(new RegistrationOtpMail($otpCode, $this->studentName));

        $this->otp = ['', '', '', '', '', ''];
        $this->dispatch('otp-sent');
    }

    public function goToStep(int $step)
    {
        if ($step === 1 && $this->currentStep === 2) {
            $this->currentStep = 1;
        } elseif ($step === 2 && $this->currentStep === 3) {
            $this->currentStep = 2;
        }
    }

    public function render()
    {
        return view('pages.auth.register');
    }
}
