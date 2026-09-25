<?php

namespace App\Livewire\Auth;

use App\Mail\RegistrationOtpMail;
use App\Models\AuditLog;
use App\Models\EligibleVoter;
use App\Models\RegistrationOtp;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\VoterAccount;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
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

    public bool $isIncompleteVoter = false;

    public array $incompleteMissingFields = [];

    public ?string $incompleteVoterNim = null;

    public ?string $incompleteVoterName = null;

    public bool $isUnregisteredVoter = false;

    public ?string $unregisteredNim = null;

    public ?string $unregisteredTitle = null;

    public ?string $unregisteredMessage = null;

    public ?string $unregisteredSubMessage = null;

    public ?string $unregisteredWaIssue = null;

    public function updatedNim(): void
    {
        $this->resetIncompleteState();
        $this->resetUnregisteredState();
    }

    public function updatedBirthDate(): void
    {
        $this->resetIncompleteState();
        $this->resetUnregisteredState();
    }

    public function resetIncompleteState(): void
    {
        $this->isIncompleteVoter = false;
        $this->incompleteMissingFields = [];
        $this->incompleteVoterNim = null;
        $this->incompleteVoterName = null;
        $this->resetErrorBag();
    }

    public function resetUnregisteredState(): void
    {
        $this->isUnregisteredVoter = false;
        $this->unregisteredNim = null;
        $this->unregisteredTitle = null;
        $this->unregisteredMessage = null;
        $this->unregisteredSubMessage = null;
        $this->unregisteredWaIssue = null;
        $this->resetErrorBag();
    }

    #[Computed]
    public function humasWhatsappUrl(): string
    {
        $phone = SystemSetting::get('humas_whatsapp') ?? env('HUMAS_WHATSAPP') ?? config('pemira.contacts.humas.whatsapp_number', '6281337534761');

        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $phone);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '628'.substr($cleanPhone, 2);
        }
        if ($cleanPhone === '' || $cleanPhone === 'REPLACE_WITH_OFFICIAL_NUMBER') {
            $cleanPhone = '6281337534761';
        }

        $voterName = $this->incompleteVoterName ?? 'Mahasiswa';
        $voterNim = $this->incompleteVoterNim ?? $this->nim;
        $missing = ! empty($this->incompleteMissingFields) ? implode(', ', $this->incompleteMissingFields) : 'informasi pemilih';

        $text = "Halo Tim Humas PEMIRA (kak Sintya), saya {$voterName} (NIM: {$voterNim}). Data pemilih saya belum lengkap ({$missing}). Mohon bantuannya untuk verifikasi dan melengkapi data agar dapat membuat akun voter.";

        return "https://wa.me/{$cleanPhone}?text=".rawurlencode($text);
    }

    #[Computed]
    public function unregisteredHumasWhatsappUrl(): string
    {
        $phone = SystemSetting::get('humas_whatsapp') ?? env('HUMAS_WHATSAPP') ?? config('pemira.contacts.humas.whatsapp_number', '6281337534761');

        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $phone);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '628'.substr($cleanPhone, 2);
        }
        if ($cleanPhone === '' || $cleanPhone === 'REPLACE_WITH_OFFICIAL_NUMBER') {
            $cleanPhone = '6281337534761';
        }

        $voterNim = trim($this->unregisteredNim ?? $this->nim);
        $nimText = $voterNim !== '' ? "NIM: {$voterNim}" : 'mahasiswa aktif';
        $issue = $this->unregisteredWaIssue ?? 'NIM saya belum terdaftar di Daftar Pemilih Tetap (DPT)';

        $text = "Halo kak Sintya (Humas PEMIRA), saya ({$nimText}) ingin mendaftar akun voter PEMIRA namun {$issue}. Mohon bantuannya untuk verifikasi status pemilih aktif. Terima kasih.";

        return "https://wa.me/{$cleanPhone}?text=".rawurlencode($text);
    }

    #[Computed]
    public function ketuaPanitiaWhatsappUrl(): string
    {
        $phone = env('KETUA_PANITIA_WHATSAPP') ?? config('pemira.contacts.ketua_panitia.whatsapp_number', '628970898383');

        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $phone);
        if (str_starts_with($cleanPhone, '08')) {
            $cleanPhone = '628'.substr($cleanPhone, 2);
        }
        if ($cleanPhone === '' || $cleanPhone === 'REPLACE_WITH_OFFICIAL_NUMBER') {
            $cleanPhone = '628970898383';
        }

        $voterNim = trim($this->unregisteredNim ?? $this->nim);
        $nimText = $voterNim !== '' ? "NIM: {$voterNim}" : 'mahasiswa aktif';
        $issue = $this->unregisteredWaIssue ?? 'NIM belum terdaftar di DPT';

        $text = "Halo kak Diana (Ketua Panitia PEMIRA), saya ({$nimText}) ingin konfirmasi pendaftaran akun voter PEMIRA karena {$issue}. Mohon bantuannya.";

        return "https://wa.me/{$cleanPhone}?text=".rawurlencode($text);
    }

    public function validateStudent()
    {
        $this->resetIncompleteState();
        $this->resetUnregisteredState();

        $this->validate([
            'nim' => ['required', 'string'],
            'birth_date' => ['required', 'date'],
        ], [
            'nim.required' => 'Nomor Induk Mahasiswa (NIM) wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'birth_date.date' => 'Format tanggal lahir tidak valid.',
        ]);

        $nim = trim((string) $this->nim);
        $voter = EligibleVoter::with('studyProgram')->where('nim', $nim)->first();

        if (! $voter) {
            $this->isUnregisteredVoter = true;
            $this->unregisteredNim = $nim;
            $this->unregisteredTitle = 'NIM BELUM TERDAFTAR DI DPT';
            $this->unregisteredMessage = "NIM ({$nim}) tidak tercatat dalam data DPT PEMIRA.";
            $this->unregisteredSubMessage = 'Pendaftaran akun hanya bagi mahasiswa aktif di DPT. Jika kamu mahasiswa aktif PNB, silakan hubungi panitia untuk verifikasi:';
            $this->unregisteredWaIssue = 'NIM saya belum terdaftar di Daftar Pemilih Tetap (DPT)';
            $this->addError('nim', 'NIM Anda belum terdaftar dalam Daftar Pemilih Tetap (DPT). Silakan hubungi Tim Humas.');

            return;
        }

        if (! $voter->is_eligible) {
            $this->isUnregisteredVoter = true;
            $this->unregisteredNim = $nim;
            $this->unregisteredTitle = 'STATUS MAHASISWA TIDAK MEMENUHI SYARAT';
            $this->unregisteredMessage = "NIM ({$nim}) berstatus tidak memenuhi syarat pemilih aktif PEMIRA.";
            $this->unregisteredSubMessage = 'Jika kamu adalah mahasiswa aktif dan merasa status ini keliru, silakan hubungi panitia untuk verifikasi:';
            $this->unregisteredWaIssue = 'status pemilih saya dinyatakan tidak memenuhi syarat aktif';
            $this->addError('nim', 'Status mahasiswa tidak memenuhi syarat sebagai pemilih aktif PEMIRA.');

            return;
        }

        if (VoterAccount::where('eligible_voter_id', $voter->id)->exists()) {
            $this->isUnregisteredVoter = true;
            $this->unregisteredNim = $nim;
            $this->unregisteredTitle = 'AKUN PEMILIH SUDAH TERDAFTAR';
            $this->unregisteredMessage = "NIM ({$nim}) sudah memiliki akun pemilih di sistem PEMIRA.";
            $this->unregisteredSubMessage = 'Kamu tidak perlu mendaftar ulang. Silakan langsung masuk ke akun pemilih atau hubungi panitia jika mengalami kendala:';
            $this->unregisteredWaIssue = 'NIM saya terdeteksi sudah memiliki akun pemilih terdaftar';
            $this->addError('nim', 'Mahasiswa dengan NIM ini sudah memiliki akun pemilih terdaftar.');

            return;
        }

        if (! $voter->isComplete()) {
            $this->isIncompleteVoter = true;
            $this->incompleteMissingFields = $voter->missingFields();
            $this->incompleteVoterNim = $voter->nim;
            $this->incompleteVoterName = $voter->name;
            $this->addError('nim', 'Data pemilih kamu belum lengkap. Silakan hubungi tim Humas / Panitia untuk melengkapi data.');

            return;
        }

        $inputDob = Carbon::parse($this->birth_date)->format('Y-m-d');
        $dbDob = $voter->date_of_birth?->format('Y-m-d');

        if ($inputDob !== $dbDob) {
            $this->isUnregisteredVoter = true;
            $this->unregisteredNim = $nim;
            $this->unregisteredTitle = 'TANGGAL LAHIR TIDAK SESUAI DPT';
            $this->unregisteredMessage = "NIM ({$nim}) terdaftar di DPT, namun tanggal lahir yang dimasukkan tidak cocok.";
            $this->unregisteredSubMessage = 'Pastikan tanggal lahir sesuai data mahasiswa aktif kamu. Jika tanggal lahir sudah benar tetapi berbeda di sistem panitia, silakan hubungi panitia untuk verifikasi:';
            $this->unregisteredWaIssue = 'tanggal lahir saya tidak cocok dengan data terdaftar di DPT';
            $this->addError('nim', 'NIM atau tanggal lahir tidak cocok dengan data pemilih aktif.');

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
