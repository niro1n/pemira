<?php

use App\Livewire\Admin\CandidatePairs\Index as CandidatePairIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Elections\Index as ElectionIndex;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Models\CandidatePair;
use App\Models\Election;
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
    if (Auth::user()->canAccessAdminPanel()) {
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

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', Dashboard::class)->name('admin.dashboard');
    Route::get('/elections', ElectionIndex::class)->name('admin.elections.index');
    Route::get('/paslon', CandidatePairIndex::class)->name('admin.candidate-pairs.index');
});
