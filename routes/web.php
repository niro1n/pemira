<?php

use App\Livewire\Admin\Admins\Index as AdminIndex;
use App\Livewire\Admin\AuditLogs\Index as AuditLogIndex;
use App\Livewire\Admin\CandidatePairs\Index as CandidatePairIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Elections\Index as ElectionIndex;
use App\Livewire\Admin\EligibleVoters\Index as EligibleVoterIndex;
use App\Livewire\Admin\Feedbacks\Index as FeedbackIndex;
use App\Livewire\Admin\SpecialActions\Index as SpecialActionIndex;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Models\CandidatePair;
use App\Models\Election;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $election = Election::current();

    if ($election) {
        $election->load([
            'candidatePairs' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('candidate_number', 'asc')
                    ->with([
                        'candidateMembers.eligibleVoter.studyProgram',
                        'candidateMissions',
                    ]);
            },
        ]);
    }

    return view('pages.home', [
        'election' => $election,
    ]);
})->name('home');

Route::get('/paslon/{candidatePair}', function (CandidatePair $candidatePair) {
    $election = Election::current();

    if (! $election || ! $candidatePair->is_active || $candidatePair->election_id !== $election->id) {
        abort(404);
    }

    $candidatePair->load([
        'candidateMembers.eligibleVoter.studyProgram',
        'election',
        'candidateMissions',
    ]);

    return view('pages.candidate-detail', [
        'candidate' => $candidatePair,
        'election' => $election,
    ]);
})->name('public.candidates.show');

Route::get('/login', Login::class)->middleware('guest')->name('login');
Route::get('/register', Register::class)->middleware('guest')->name('register');
Route::get('/forgot-password', ForgotPassword::class)->middleware('guest')->name('password.request');
Route::get('/reset-password/{token}', ResetPassword::class)->middleware('guest')->name('password.reset');

Route::get('/profile', function () {
    $user = Auth::user();

    if ($user instanceof User && $user->canAccessAdminPanel()) {
        return redirect('/admin');
    }

    return redirect()->route('home');
})->middleware('auth')->name('profile');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->middleware('auth')->name('logout');

Route::get('/maintenance', function () {
    if (! SystemSetting::isMaintenanceMode()) {
        return redirect()->route('home');
    }

    return response()->view('pages.maintenance', [], 503);
})->name('maintenance');

Route::get('/tindakan-khusus', function () {
    return redirect()->route('admin.special-actions.index');
});

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', Dashboard::class)->name('admin.dashboard');
    Route::get('/elections', ElectionIndex::class)->name('admin.elections.index');
    Route::get('/paslon', CandidatePairIndex::class)->name('admin.candidate-pairs.index');
    Route::get('/eligible-voters', EligibleVoterIndex::class)->name('admin.eligible-voters.index');
    Route::get('/masukan-pemilih', FeedbackIndex::class)->name('admin.feedbacks.index');
    Route::get('/audit-logs', AuditLogIndex::class)->name('admin.audit-logs.index');
    Route::get('/admins', AdminIndex::class)->middleware('role:super_admin')->name('admin.admins.index');
    Route::get('/tindakan-khusus', SpecialActionIndex::class)->middleware('role:super_admin')->name('admin.special-actions.index');
});
