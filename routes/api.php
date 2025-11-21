<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API routes
Route::prefix('v1')->group(function () {
    // Health check
    Route::get('/health', function () {
        return response()->json(['status' => 'ok', 'timestamp' => now()]);
    });

    // PesePay webhook
    Route::post('/payments/webhook/pesepay', function (Request $request) {
        // TODO: Implement PesePay webhook handler
        return response()->json(['status' => 'received']);
    })->name('api.payments.webhook.pesepay');
});

// Authenticated API routes
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // User
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles');
    });

    // Players
    Route::apiResource('players', 'App\Http\Controllers\Api\PlayerController');
    Route::post('/players/{player}/documents', 'App\Http\Controllers\Api\PlayerController@uploadDocument');
    Route::post('/players/{player}/approve', 'App\Http\Controllers\Api\PlayerController@approve');
    Route::post('/players/{player}/reject', 'App\Http\Controllers\Api\PlayerController@reject');

    // Clubs
    Route::apiResource('clubs', 'App\Http\Controllers\Api\ClubController');
    Route::post('/clubs/{club}/documents', 'App\Http\Controllers\Api\ClubController@uploadDocument');
    Route::post('/clubs/{club}/officials', 'App\Http\Controllers\Api\ClubController@addOfficial');
    Route::get('/clubs/{club}/players', 'App\Http\Controllers\Api\ClubController@players');

    // Transfers
    Route::apiResource('transfers', 'App\Http\Controllers\Api\TransferController');
    Route::post('/transfers/{transfer}/approve-from-club', 'App\Http\Controllers\Api\TransferController@approveFromClub');
    Route::post('/transfers/{transfer}/approve-zifa', 'App\Http\Controllers\Api\TransferController@approveZifa');
    Route::post('/transfers/{transfer}/reject', 'App\Http\Controllers\Api\TransferController@reject');

    // Invoices & Payments
    Route::apiResource('invoices', 'App\Http\Controllers\Api\InvoiceController');
    Route::post('/invoices/{invoice}/pay', 'App\Http\Controllers\Api\PaymentController@initiate');
    Route::get('/payments/{payment}/status', 'App\Http\Controllers\Api\PaymentController@status');

    // Competitions
    Route::apiResource('competitions', 'App\Http\Controllers\Api\CompetitionController');
    Route::get('/competitions/{competition}/standings', 'App\Http\Controllers\Api\CompetitionController@standings');
    Route::get('/competitions/{competition}/matches', 'App\Http\Controllers\Api\CompetitionController@matches');

    // Matches
    Route::apiResource('matches', 'App\Http\Controllers\Api\MatchController');
    Route::post('/matches/{match}/squad', 'App\Http\Controllers\Api\MatchController@submitSquad');
    Route::post('/matches/{match}/events', 'App\Http\Controllers\Api\MatchController@addEvent');
    Route::post('/matches/{match}/report', 'App\Http\Controllers\Api\MatchController@submitReport');

    // Officials & Referees
    Route::apiResource('officials', 'App\Http\Controllers\Api\OfficialController');
    Route::apiResource('referees', 'App\Http\Controllers\Api\RefereeController');

    // Disciplinary
    Route::apiResource('disciplinary-cases', 'App\Http\Controllers\Api\DisciplinaryCaseController');
    Route::post('/disciplinary-cases/{case}/sanctions', 'App\Http\Controllers\Api\DisciplinaryCaseController@addSanction');

    // Registrations & Affiliations
    Route::apiResource('registrations', 'App\Http\Controllers\Api\RegistrationController');
    Route::post('/registrations/{registration}/approve', 'App\Http\Controllers\Api\RegistrationController@approve');
    Route::post('/registrations/{registration}/reject', 'App\Http\Controllers\Api\RegistrationController@reject');

    // Funds
    Route::apiResource('funds', 'App\Http\Controllers\Api\FundController');
    Route::post('/funds/{fund}/disburse', 'App\Http\Controllers\Api\FundController@disburse');

    // Regions
    Route::apiResource('regions', 'App\Http\Controllers\Api\RegionController');

    // Training Courses
    Route::apiResource('courses', 'App\Http\Controllers\Api\TrainingCourseController');
    Route::post('/courses/{course}/enroll', 'App\Http\Controllers\Api\TrainingCourseController@enroll');

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/dashboard', 'App\Http\Controllers\Api\ReportController@dashboard');
        Route::get('/financial', 'App\Http\Controllers\Api\ReportController@financial');
        Route::get('/registrations', 'App\Http\Controllers\Api\ReportController@registrations');
        Route::get('/transfers', 'App\Http\Controllers\Api\ReportController@transfers');
    });

    // FIFA Sync
    Route::prefix('fifa')->group(function () {
        Route::get('/sync-status', 'App\Http\Controllers\Api\FifaSyncController@status');
        Route::post('/sync/{entity}/{id}', 'App\Http\Controllers\Api\FifaSyncController@sync');
        Route::get('/mismatches', 'App\Http\Controllers\Api\FifaSyncController@mismatches');
    });

    // System settings (admin only)
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/settings', 'App\Http\Controllers\Api\SettingController@index');
        Route::put('/settings', 'App\Http\Controllers\Api\SettingController@update');
    });
});
