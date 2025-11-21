<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->name('register');

// Payment callbacks
Route::get('/payments/callback', function () {
    return Inertia::render('Payments/Callback');
})->name('payments.callback');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Players
    Route::prefix('players')->name('players.')->group(function () {
        Route::get('/', fn() => Inertia::render('Players/Index'))->name('index');
        Route::get('/create', fn() => Inertia::render('Players/Create'))->name('create');
        Route::get('/{player}', fn() => Inertia::render('Players/Show'))->name('show');
        Route::get('/{player}/edit', fn() => Inertia::render('Players/Edit'))->name('edit');
    });

    // Clubs
    Route::prefix('clubs')->name('clubs.')->group(function () {
        Route::get('/', fn() => Inertia::render('Clubs/Index'))->name('index');
        Route::get('/create', fn() => Inertia::render('Clubs/Create'))->name('create');
        Route::get('/{club}', fn() => Inertia::render('Clubs/Show'))->name('show');
        Route::get('/{club}/edit', fn() => Inertia::render('Clubs/Edit'))->name('edit');
    });

    // Transfers
    Route::prefix('transfers')->name('transfers.')->group(function () {
        Route::get('/', fn() => Inertia::render('Transfers/Index'))->name('index');
        Route::get('/create', fn() => Inertia::render('Transfers/Create'))->name('create');
        Route::get('/{transfer}', fn() => Inertia::render('Transfers/Show'))->name('show');
    });

    // Competitions
    Route::prefix('competitions')->name('competitions.')->group(function () {
        Route::get('/', fn() => Inertia::render('Competitions/Index'))->name('index');
        Route::get('/{competition}', fn() => Inertia::render('Competitions/Show'))->name('show');
        Route::get('/{competition}/matches', fn() => Inertia::render('Competitions/Matches'))->name('matches');
        Route::get('/{competition}/standings', fn() => Inertia::render('Competitions/Standings'))->name('standings');
    });

    // Officials & Referees
    Route::prefix('officials')->name('officials.')->group(function () {
        Route::get('/', fn() => Inertia::render('Officials/Index'))->name('index');
        Route::get('/create', fn() => Inertia::render('Officials/Create'))->name('create');
    });

    Route::prefix('referees')->name('referees.')->group(function () {
        Route::get('/', fn() => Inertia::render('Referees/Index'))->name('index');
        Route::get('/create', fn() => Inertia::render('Referees/Create'))->name('create');
    });

    // Payments & Invoices
    Route::prefix('invoices')->name('invoices.')->group(function () {
        Route::get('/', fn() => Inertia::render('Invoices/Index'))->name('index');
        Route::get('/{invoice}', fn() => Inertia::render('Invoices/Show'))->name('show');
        Route::get('/{invoice}/pay', fn() => Inertia::render('Invoices/Pay'))->name('pay');
    });

    // Disciplinary
    Route::prefix('disciplinary')->name('disciplinary.')->group(function () {
        Route::get('/', fn() => Inertia::render('Disciplinary/Index'))->name('index');
        Route::get('/create', fn() => Inertia::render('Disciplinary/Create'))->name('create');
        Route::get('/{case}', fn() => Inertia::render('Disciplinary/Show'))->name('show');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', fn() => Inertia::render('Reports/Index'))->name('index');
        Route::get('/financial', fn() => Inertia::render('Reports/Financial'))->name('financial');
        Route::get('/registrations', fn() => Inertia::render('Reports/Registrations'))->name('registrations');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', fn() => Inertia::render('Settings/Index'))->name('index');
        Route::get('/profile', fn() => Inertia::render('Settings/Profile'))->name('profile');
        Route::get('/system', fn() => Inertia::render('Settings/System'))->name('system');
    });

    // Fan Zone
    Route::prefix('fan')->name('fan.')->group(function () {
        // News
        Route::get('/news', fn() => Inertia::render('Fan/News/Index'))->name('news.index');
        Route::get('/news/create', fn() => Inertia::render('Fan/News/Create'))->name('news.create');
        Route::get('/news/{news}', fn() => Inertia::render('Fan/News/Show'))->name('news.show');
        Route::get('/news/{news}/edit', fn() => Inertia::render('Fan/News/Edit'))->name('news.edit');

        // Polls
        Route::get('/polls', fn() => Inertia::render('Fan/Polls/Index'))->name('polls.index');
        Route::get('/polls/create', fn() => Inertia::render('Fan/Polls/Create'))->name('polls.create');
        Route::get('/polls/{poll}', fn() => Inertia::render('Fan/Polls/Show'))->name('polls.show');

        // Profile
        Route::get('/profile', fn() => Inertia::render('Fan/Profile/Index'))->name('profile.index');
        Route::get('/profile/create', fn() => Inertia::render('Fan/Profile/Create'))->name('profile.create');
        Route::get('/profile/edit', fn() => Inertia::render('Fan/Profile/Edit'))->name('profile.edit');

        // Leaderboard
        Route::get('/leaderboard', fn() => Inertia::render('Fan/Leaderboard'))->name('leaderboard');
    });
});

// Admin routes
Route::middleware(['auth', 'verified', 'role:super_admin,zifa_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn() => Inertia::render('Admin/Dashboard'))->name('dashboard');
    Route::get('/users', fn() => Inertia::render('Admin/Users/Index'))->name('users.index');
    Route::get('/audit-logs', fn() => Inertia::render('Admin/AuditLogs'))->name('audit-logs');
    Route::get('/fifa-sync', fn() => Inertia::render('Admin/FifaSync'))->name('fifa-sync');
});
