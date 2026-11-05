<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\EligibleVoters\Index as EligibleVoterIndex;
use App\Livewire\Admin\SpecialActions\Index as SpecialActionIndex;
use App\Livewire\Auth\Register;
use App\Models\AuditLog;
use App\Models\Election;
use App\Models\EligibleVoter;
use App\Models\RegistrationOtp;
use App\Models\StudyProgram;
use App\Models\User;
use App\Models\VoterAccount;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SuperAdminManualVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $voterUser;

    protected StudyProgram $studyProgram;

    protected EligibleVoter $eligibleVoter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        $this->admin = User::create([
            'name' => 'Panitia Admin',
            'email' => 'admin@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->voterUser = User::create([
            'name' => 'Mahasiswa Voter',
            'email' => 'voter@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);

        $this->studyProgram = StudyProgram::create([
            'code' => 'TI',
            'name' => 'Teknologi Informasi',
        ]);

        $this->eligibleVoter = EligibleVoter::create([
            'nim' => '2115354001',
            'name' => 'Irma Suryani',
            'date_of_birth' => '2003-05-12',
            'study_program_id' => $this->studyProgram->id,
            'is_eligible' => true,
        ]);
    }

    public function test_super_admin_can_generate_manual_otp_and_it_is_logged(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(SpecialActionIndex::class)
            ->set('otpEmail', 'irma@student.pnb.ac.id')
            ->set('manualOtpCode', '654321')
            ->call('generateManualOtp')
            ->assertHasNoErrors()
            ->assertSet('generatedOtpResult', '654321')
            ->assertSet('showOtpSuccessModal', true);

        $otp = RegistrationOtp::where('email', 'irma@student.pnb.ac.id')->first();
        $this->assertNotNull($otp);
        $this->assertTrue($otp->verify('654321'));
        $this->assertFalse($otp->isExpired());

        $log = AuditLog::where('action', 'superadmin_generated_registration_otp')->latest()->first();
        $this->assertNotNull($log);
        $this->assertEquals($this->superAdmin->id, $log->user_id);
        $this->assertEquals('irma@student.pnb.ac.id', $log->metadata['email']);
        $this->assertEquals('654321', $log->metadata['otp_code']);
    }

    public function test_generated_manual_otp_can_be_used_by_student_to_complete_registration(): void
    {
        $now = now();
        $election = Election::create([
            'name' => 'PEMIRA 2026',
            'slug' => 'pemira-2026',
            'year' => 2026,
            'registration_start_at' => $now->copy()->subDay(),
            'registration_end_at' => $now->copy()->addDays(2),
            'voting_start_at' => $now->copy()->addDays(3),
            'voting_end_at' => $now->copy()->addDays(4),
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(SpecialActionIndex::class)
            ->set('otpEmail', 'irma@student.pnb.ac.id')
            ->set('manualOtpCode', '987654')
            ->call('generateManualOtp');

        Livewire::test(Register::class)
            ->set('currentStep', 3)
            ->set('eligibleVoterId', $this->eligibleVoter->id)
            ->set('studentName', $this->eligibleVoter->name)
            ->set('email', 'irma@student.pnb.ac.id')
            ->set('password', 'Rahasia123!')
            ->set('otp', ['9', '8', '7', '6', '5', '4'])
            ->call('verifyOtp')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 4);

        $newUser = User::where('email', 'irma@student.pnb.ac.id')->first();
        $this->assertNotNull($newUser);
        $this->assertTrue($newUser->isVoter());
        $this->assertNotNull($newUser->email_verified_at);

        $voterAccount = VoterAccount::where('user_id', $newUser->id)->first();
        $this->assertNotNull($voterAccount);
        $this->assertEquals($this->eligibleVoter->id, $voterAccount->eligible_voter_id);
    }

    public function test_super_admin_can_manually_activate_voter_account_directly(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(SpecialActionIndex::class)
            ->call('selectVoterForActivation', $this->eligibleVoter->id)
            ->assertSet('showActivationModal', true)
            ->set('activationEmail', 'irma.direct@student.pnb.ac.id')
            ->set('activationPassword', 'PasswordBaru123!')
            ->set('activationNote', 'Validasi fisik via KTM WhatsApp')
            ->call('activateVoterAccount')
            ->assertHasNoErrors()
            ->assertSet('showActivationSuccessModal', true);

        $user = User::where('email', 'irma.direct@student.pnb.ac.id')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('PasswordBaru123!', $user->password));
        $this->assertTrue($user->isVoter());
        $this->assertNotNull($user->email_verified_at);

        $voterAccount = VoterAccount::where('user_id', $user->id)->first();
        $this->assertNotNull($voterAccount);
        $this->assertEquals($this->eligibleVoter->id, $voterAccount->eligible_voter_id);

        $log = AuditLog::where('action', 'superadmin_manual_voter_activation')->latest()->first();
        $this->assertNotNull($log);
        $this->assertEquals($this->superAdmin->id, $log->user_id);
        $this->assertEquals($this->eligibleVoter->nim, $log->metadata['nim']);

        $this->assertTrue(Auth::attempt([
            'email' => 'irma.direct@student.pnb.ac.id',
            'password' => 'PasswordBaru123!',
        ]));
    }

    public function test_super_admin_can_activate_from_eligible_voters_page(): void
    {
        Livewire::actingAs($this->superAdmin)
            ->test(EligibleVoterIndex::class)
            ->call('openSuperAdminActivationModal', $this->eligibleVoter->id)
            ->assertSet('showSuperAdminActivationModal', true)
            ->set('superAdminActivationEmail', 'irma.table@student.pnb.ac.id')
            ->set('superAdminActivationPassword', 'RahasiaTable123!')
            ->set('superAdminActivationNote', 'Aktivasi langsung dari tabel DPT')
            ->call('confirmSuperAdminActivation')
            ->assertHasNoErrors()
            ->assertSet('showSuperAdminSuccessModal', true);

        $user = User::where('email', 'irma.table@student.pnb.ac.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Irma Suryani', $user->name);
    }

    public function test_non_super_admin_cannot_execute_manual_activation_methods(): void
    {
        Livewire::actingAs($this->admin)
            ->test(EligibleVoterIndex::class)
            ->call('openSuperAdminActivationModal', $this->eligibleVoter->id)
            ->assertForbidden();

        Livewire::actingAs($this->admin)
            ->test(SpecialActionIndex::class)
            ->assertForbidden();
    }

    public function test_non_super_admin_cannot_access_special_actions_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/special-actions');
        $response->assertForbidden();

        $voterResponse = $this->actingAs($this->voterUser)->get('/admin/special-actions');
        $voterResponse->assertForbidden();
    }
}

