<?php

namespace Tests\Feature\Admin;

use App\Enums\AdminInvitationStatus;
use App\Livewire\Admin\Admins\Index as AdminIndex;
use App\Livewire\Auth\AcceptInvitation;
use App\Mail\AdminInvitationMail;
use App\Models\AdminInvitation;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class AdminInvitationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $voterUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'name' => 'Super Admin Test',
            'email' => 'superadmin@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        $this->admin = User::create([
            'name' => 'Admin Operator Test',
            'email' => 'admin@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $this->voterUser = User::create([
            'email' => 'voter@pemira.test',
            'password' => Hash::make('password123'),
            'role' => 'voter',
            'email_verified_at' => now(),
        ]);
    }

    public function test_super_admin_can_view_admin_invitations_section(): void
    {
        $invitation = AdminInvitation::create([
            'email' => 'calon.admin@pemira.test',
            'token' => AdminInvitation::hashToken('dummy-plain-token'),
            'expires_at' => now()->addHours(24),
            'invited_by' => $this->superAdmin->id,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->assertSee('ADMIN INVITATIONS')
            ->assertSee('UNDANG ADMIN')
            ->assertSee('calon.admin@pemira.test')
            ->assertSee('MENUNGGU')
            ->assertSet('statistics.pending_invitations_count', 1);
    }

    public function test_super_admin_can_send_invitation_with_secure_hashed_token_and_email(): void
    {
        Mail::fake();

        $calonEmail = 'calon.baru@pemira.test';

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('openInviteModal')
            ->assertSet('showInviteModal', true)
            ->set('inviteEmail', $calonEmail)
            ->call('sendInvitation')
            ->assertSet('showInviteModal', false)
            ->assertHasNoErrors();

        $this->assertDatabaseHas('admin_invitations', [
            'email' => $calonEmail,
            'invited_by' => $this->superAdmin->id,
            'accepted_at' => null,
            'revoked_at' => null,
        ]);

        $invitation = AdminInvitation::where('email', $calonEmail)->first();
        $this->assertNotNull($invitation);
        $this->assertEquals(64, strlen($invitation->token)); // SHA-256 hash length
        $this->assertTrue($invitation->expires_at->isFuture());

        Mail::assertQueued(AdminInvitationMail::class, function ($mail) use ($calonEmail, $invitation) {
            $hasRecipient = $mail->hasTo($calonEmail);
            $plainToken = $mail->plainToken;

            // Database MUST NOT store plaintext token
            $this->assertNotEquals($plainToken, $invitation->token);
            // Plain token hashed must match DB token
            $this->assertEquals(hash('sha256', $plainToken), $invitation->token);

            return $hasRecipient;
        });

        // Audit log created without token leakage
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->superAdmin->id,
            'action' => 'admin_invitation_sent',
            'entity_type' => 'AdminInvitation',
            'entity_id' => $invitation->id,
        ]);

        $auditLog = AuditLog::where('action', 'admin_invitation_sent')->first();
        $this->assertStringNotContainsString($invitation->token, json_encode($auditLog->metadata));
    }

    public function test_cannot_send_invitation_if_email_is_already_registered_user(): void
    {
        Mail::fake();

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('openInviteModal')
            ->set('inviteEmail', $this->admin->email)
            ->call('sendInvitation')
            ->assertHasErrors(['inviteEmail']);

        Mail::assertNothingSent();
    }

    public function test_cannot_send_duplicate_invitation_if_email_has_pending_invitation(): void
    {
        Mail::fake();

        AdminInvitation::create([
            'email' => 'calon.pending@pemira.test',
            'token' => AdminInvitation::hashToken('some-token'),
            'expires_at' => now()->addHours(24),
            'invited_by' => $this->superAdmin->id,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('openInviteModal')
            ->set('inviteEmail', 'calon.pending@pemira.test')
            ->call('sendInvitation')
            ->assertHasErrors(['inviteEmail']);

        Mail::assertNothingSent();
    }

    public function test_super_admin_can_resend_invitation_which_regenerates_token(): void
    {
        Mail::fake();
        RateLimiter::clear('resend-admin-invitation:1');

        $plainOld = AdminInvitation::generatePlainToken();
        $invitation = AdminInvitation::create([
            'email' => 'calon.resend@pemira.test',
            'token' => AdminInvitation::hashToken($plainOld),
            'expires_at' => now()->addHours(2),
            'invited_by' => $this->superAdmin->id,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('resendInvitation', $invitation->id)
            ->assertHasNoErrors();

        $invitation->refresh();
        $this->assertNotEquals(AdminInvitation::hashToken($plainOld), $invitation->token);
        $this->assertTrue($invitation->expires_at->gt(now()->addHours(23)));

        Mail::assertQueued(AdminInvitationMail::class, function ($mail) use ($invitation) {
            return $mail->hasTo($invitation->email)
                && hash('sha256', $mail->plainToken) === $invitation->token;
        });

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->superAdmin->id,
            'action' => 'admin_invitation_resent',
            'entity_type' => 'AdminInvitation',
            'entity_id' => $invitation->id,
        ]);
    }

    public function test_resend_invitation_is_rate_limited(): void
    {
        Mail::fake();

        $invitation = AdminInvitation::create([
            'email' => 'calon.throttled@pemira.test',
            'token' => AdminInvitation::hashToken(AdminInvitation::generatePlainToken()),
            'expires_at' => now()->addHours(24),
            'invited_by' => $this->superAdmin->id,
        ]);

        RateLimiter::clear('resend-admin-invitation:'.$invitation->id);

        $component = Livewire::actingAs($this->superAdmin)->test(AdminIndex::class);

        // First attempt succeeds
        $component->call('resendInvitation', $invitation->id);
        Mail::assertQueued(AdminInvitationMail::class, 1);

        // Immediate second attempt gets rate limited
        $component->call('resendInvitation', $invitation->id);
        Mail::assertQueued(AdminInvitationMail::class, 1); // No new email sent
    }

    public function test_super_admin_can_revoke_pending_invitation(): void
    {
        $invitation = AdminInvitation::create([
            'email' => 'calon.revoke@pemira.test',
            'token' => AdminInvitation::hashToken(AdminInvitation::generatePlainToken()),
            'expires_at' => now()->addHours(24),
            'invited_by' => $this->superAdmin->id,
        ]);

        Livewire::actingAs($this->superAdmin)
            ->test(AdminIndex::class)
            ->call('openRevokeModal', $invitation->id)
            ->assertSet('showRevokeModal', true)
            ->assertSet('selectedInvitationId', $invitation->id)
            ->call('revokeInvitation')
            ->assertSet('showRevokeModal', false);

        $invitation->refresh();
        $this->assertNotNull($invitation->revoked_at);
        $this->assertEquals(AdminInvitationStatus::REVOKED, $invitation->status());

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->superAdmin->id,
            'action' => 'admin_invitation_revoked',
            'entity_type' => 'AdminInvitation',
            'entity_id' => $invitation->id,
        ]);
    }

    public function test_guest_can_access_invitation_page_with_valid_token(): void
    {
        $plainToken = AdminInvitation::generatePlainToken();
        $invitation = AdminInvitation::create([
            'email' => 'target.accept@pemira.test',
            'token' => AdminInvitation::hashToken($plainToken),
            'expires_at' => now()->addHours(24),
            'invited_by' => $this->superAdmin->id,
        ]);

        $response = $this->get(route('admin.invitations.accept', ['token' => $plainToken]));

        $response->assertOk();
        $response->assertSeeLivewire(AcceptInvitation::class);
        $response->assertSee('BUAT AKUN ADMIN');
        $response->assertSee('target.accept@pemira.test');
    }

    public function test_invalid_token_shows_not_found_status(): void
    {
        Livewire::test(AcceptInvitation::class, ['token' => 'invalid-random-token'])
            ->assertSet('invitationStatus', 'not_found')
            ->assertSee('STATUS UNDANGAN TIDAK VALID')
            ->assertSee('Tautan Undangan Tidak Ditemukan');
    }

    public function test_expired_token_shows_expired_status(): void
    {
        $plainToken = AdminInvitation::generatePlainToken();
        AdminInvitation::create([
            'email' => 'target.expired@pemira.test',
            'token' => AdminInvitation::hashToken($plainToken),
            'expires_at' => now()->subHour(),
            'invited_by' => $this->superAdmin->id,
        ]);

        Livewire::test(AcceptInvitation::class, ['token' => $plainToken])
            ->assertSet('invitationStatus', 'expired')
            ->assertSee('Undangan Telah Kedaluwarsa');
    }

    public function test_revoked_token_shows_revoked_status(): void
    {
        $plainToken = AdminInvitation::generatePlainToken();
        AdminInvitation::create([
            'email' => 'target.revoked@pemira.test',
            'token' => AdminInvitation::hashToken($plainToken),
            'expires_at' => now()->addHours(24),
            'revoked_at' => now(),
            'invited_by' => $this->superAdmin->id,
        ]);

        Livewire::test(AcceptInvitation::class, ['token' => $plainToken])
            ->assertSet('invitationStatus', 'revoked')
            ->assertSee('Undangan Telah Dibatalkan');
    }

    public function test_already_accepted_token_shows_already_accepted_status(): void
    {
        $plainToken = AdminInvitation::generatePlainToken();
        AdminInvitation::create([
            'email' => 'target.used@pemira.test',
            'token' => AdminInvitation::hashToken($plainToken),
            'expires_at' => now()->addHours(24),
            'accepted_at' => now()->subMinutes(10),
            'invited_by' => $this->superAdmin->id,
        ]);

        Livewire::test(AcceptInvitation::class, ['token' => $plainToken])
            ->assertSet('invitationStatus', 'already_accepted')
            ->assertSee('Undangan Sudah Pernah Digunakan');
    }

    public function test_calon_admin_can_create_admin_account_atomically(): void
    {
        $plainToken = AdminInvitation::generatePlainToken();
        $invitation = AdminInvitation::create([
            'email' => 'calon.sukses@pemira.test',
            'token' => AdminInvitation::hashToken($plainToken),
            'expires_at' => now()->addHours(24),
            'invited_by' => $this->superAdmin->id,
        ]);

        Livewire::test(AcceptInvitation::class, ['token' => $plainToken])
            ->assertSet('email', 'calon.sukses@pemira.test')
            ->set('name', 'I Wayan Administrator')
            ->set('password', 'SuperRahasia123!')
            ->set('password_confirmation', 'SuperRahasia123!')
            ->call('createAccount')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));

        // User created with role admin and name
        $this->assertDatabaseHas('users', [
            'name' => 'I Wayan Administrator',
            'email' => 'calon.sukses@pemira.test',
            'role' => 'admin',
        ]);

        $createdUser = User::where('email', 'calon.sukses@pemira.test')->first();
        $this->assertNotNull($createdUser->email_verified_at);
        $this->assertTrue(Hash::check('SuperRahasia123!', $createdUser->password));

        // Invitation marked as accepted
        $invitation->refresh();
        $this->assertNotNull($invitation->accepted_at);
        $this->assertTrue($invitation->isAccepted());

        // Audit log created
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $createdUser->id,
            'action' => 'admin_invitation_accepted',
            'entity_type' => 'User',
            'entity_id' => (string) $createdUser->id,
        ]);

        // User is authenticated
        $this->assertTrue(Auth::check());
        $this->assertEquals($createdUser->id, Auth::id());
    }

    public function test_token_cannot_be_reused_after_acceptance(): void
    {
        $plainToken = AdminInvitation::generatePlainToken();
        $invitation = AdminInvitation::create([
            'email' => 'calon.singleuse@pemira.test',
            'token' => AdminInvitation::hashToken($plainToken),
            'expires_at' => now()->addHours(24),
            'invited_by' => $this->superAdmin->id,
        ]);

        // First acceptance
        Livewire::test(AcceptInvitation::class, ['token' => $plainToken])
            ->set('name', 'Admin Pertama')
            ->set('password', 'PasswordAman123!')
            ->set('password_confirmation', 'PasswordAman123!')
            ->call('createAccount')
            ->assertRedirect(route('admin.dashboard'));

        Auth::logout();

        // Subsequent attempt with same token must fail
        Livewire::test(AcceptInvitation::class, ['token' => $plainToken])
            ->assertSet('invitationStatus', 'already_accepted')
            ->assertSee('Undangan Sudah Pernah Digunakan');
    }
}
