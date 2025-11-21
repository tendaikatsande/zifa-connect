<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API routes (no authentication required)
Route::prefix('v1')->group(function () {
    // Health check
    Route::get('/health', function () {
        return response()->json(['status' => 'ok', 'timestamp' => now()]);
    });

    // PesePay webhook - must be public for payment callbacks
    Route::post('/payments/webhook/pesepay', [PaymentController::class, 'webhook'])
        ->name('api.payments.webhook.pesepay');
});

// Authenticated API routes
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // User
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles', 'permissions');
    });

    // ============================
    // PLAYERS
    // ============================
    Route::middleware(['permission:players.view'])->group(function () {
        Route::get('/players', 'App\Http\Controllers\Api\PlayerController@index');
        Route::get('/players/{player}', 'App\Http\Controllers\Api\PlayerController@show');
    });

    Route::middleware(['permission:players.create'])->group(function () {
        Route::post('/players', 'App\Http\Controllers\Api\PlayerController@store');
    });

    Route::middleware(['permission:players.update'])->group(function () {
        Route::patch('/players/{player}', 'App\Http\Controllers\Api\PlayerController@update');
        Route::put('/players/{player}', 'App\Http\Controllers\Api\PlayerController@update');
    });

    Route::middleware(['permission:players.delete'])->group(function () {
        Route::delete('/players/{player}', 'App\Http\Controllers\Api\PlayerController@destroy');
    });

    Route::middleware(['permission:players.upload_documents'])->group(function () {
        Route::post('/players/{player}/documents', 'App\Http\Controllers\Api\PlayerController@uploadDocument');
    });

    Route::middleware(['permission:players.approve'])->group(function () {
        Route::post('/players/{player}/approve', 'App\Http\Controllers\Api\PlayerController@approve');
    });

    Route::middleware(['permission:players.reject'])->group(function () {
        Route::post('/players/{player}/reject', 'App\Http\Controllers\Api\PlayerController@reject');
    });

    // ============================
    // CLUBS
    // ============================
    Route::middleware(['permission:clubs.view'])->group(function () {
        Route::get('/clubs', 'App\Http\Controllers\Api\ClubController@index');
        Route::get('/clubs/{club}', 'App\Http\Controllers\Api\ClubController@show');
        Route::get('/clubs/{club}/players', 'App\Http\Controllers\Api\ClubController@players');
    });

    Route::middleware(['permission:clubs.create'])->group(function () {
        Route::post('/clubs', 'App\Http\Controllers\Api\ClubController@store');
    });

    Route::middleware(['permission:clubs.update'])->group(function () {
        Route::patch('/clubs/{club}', 'App\Http\Controllers\Api\ClubController@update');
        Route::put('/clubs/{club}', 'App\Http\Controllers\Api\ClubController@update');
    });

    Route::middleware(['permission:clubs.upload_documents'])->group(function () {
        Route::post('/clubs/{club}/documents', 'App\Http\Controllers\Api\ClubController@uploadDocument');
    });

    Route::middleware(['permission:clubs.manage_officials'])->group(function () {
        Route::post('/clubs/{club}/officials', 'App\Http\Controllers\Api\ClubController@addOfficial');
    });

    // ============================
    // TRANSFERS
    // ============================
    Route::middleware(['permission:transfers.view'])->group(function () {
        Route::get('/transfers', 'App\Http\Controllers\Api\TransferController@index');
        Route::get('/transfers/{transfer}', 'App\Http\Controllers\Api\TransferController@show');
    });

    Route::middleware(['permission:transfers.create'])->group(function () {
        Route::post('/transfers', 'App\Http\Controllers\Api\TransferController@store');
    });

    Route::middleware(['permission:transfers.approve_club'])->group(function () {
        Route::post('/transfers/{transfer}/approve-from-club', 'App\Http\Controllers\Api\TransferController@approveFromClub');
    });

    Route::middleware(['permission:transfers.approve_zifa'])->group(function () {
        Route::post('/transfers/{transfer}/approve-zifa', 'App\Http\Controllers\Api\TransferController@approveZifa');
    });

    Route::middleware(['permission:transfers.reject'])->group(function () {
        Route::post('/transfers/{transfer}/reject', 'App\Http\Controllers\Api\TransferController@reject');
    });

    // ============================
    // INVOICES & PAYMENTS
    // ============================
    Route::middleware(['permission:invoices.view'])->group(function () {
        Route::get('/invoices', 'App\Http\Controllers\Api\InvoiceController@index');
        Route::get('/invoices/{invoice}', 'App\Http\Controllers\Api\InvoiceController@show');
    });

    Route::middleware(['permission:invoices.create'])->group(function () {
        Route::post('/invoices', 'App\Http\Controllers\Api\InvoiceController@store');
    });

    Route::middleware(['permission:invoices.cancel'])->group(function () {
        Route::post('/invoices/{invoice}/cancel', 'App\Http\Controllers\Api\InvoiceController@cancel');
    });

    Route::middleware(['permission:payments.initiate'])->group(function () {
        Route::post('/invoices/{invoice}/pay', [PaymentController::class, 'initiate']);
    });

    Route::middleware(['permission:payments.view'])->group(function () {
        Route::get('/payments/{payment}/status', [PaymentController::class, 'status']);
    });

    // ============================
    // COMPETITIONS
    // ============================
    Route::middleware(['permission:competitions.view'])->group(function () {
        Route::get('/competitions', 'App\Http\Controllers\Api\CompetitionController@index');
        Route::get('/competitions/{competition}', 'App\Http\Controllers\Api\CompetitionController@show');
        Route::get('/competitions/{competition}/standings', 'App\Http\Controllers\Api\CompetitionController@standings');
        Route::get('/competitions/{competition}/matches', 'App\Http\Controllers\Api\CompetitionController@matches');
    });

    Route::middleware(['permission:competitions.create'])->group(function () {
        Route::post('/competitions', 'App\Http\Controllers\Api\CompetitionController@store');
    });

    Route::middleware(['permission:competitions.update'])->group(function () {
        Route::patch('/competitions/{competition}', 'App\Http\Controllers\Api\CompetitionController@update');
        Route::put('/competitions/{competition}', 'App\Http\Controllers\Api\CompetitionController@update');
    });

    Route::middleware(['permission:competitions.delete'])->group(function () {
        Route::delete('/competitions/{competition}', 'App\Http\Controllers\Api\CompetitionController@destroy');
    });

    // ============================
    // MATCHES
    // ============================
    Route::middleware(['permission:matches.view'])->group(function () {
        Route::get('/matches', 'App\Http\Controllers\Api\MatchController@index');
        Route::get('/matches/{match}', 'App\Http\Controllers\Api\MatchController@show');
    });

    Route::middleware(['permission:matches.create'])->group(function () {
        Route::post('/matches', 'App\Http\Controllers\Api\MatchController@store');
    });

    Route::middleware(['permission:matches.manage'])->group(function () {
        Route::post('/matches/{match}/squad', 'App\Http\Controllers\Api\MatchController@submitSquad');
        Route::post('/matches/{match}/events', 'App\Http\Controllers\Api\MatchController@addEvent');
        Route::post('/matches/{match}/report', 'App\Http\Controllers\Api\MatchController@submitReport');
    });

    // ============================
    // OFFICIALS & REFEREES
    // ============================
    Route::middleware(['permission:officials.view'])->group(function () {
        Route::get('/officials', 'App\Http\Controllers\Api\OfficialController@index');
        Route::get('/officials/{official}', 'App\Http\Controllers\Api\OfficialController@show');
    });

    Route::middleware(['permission:officials.create'])->group(function () {
        Route::post('/officials', 'App\Http\Controllers\Api\OfficialController@store');
    });

    Route::middleware(['permission:officials.update'])->group(function () {
        Route::patch('/officials/{official}', 'App\Http\Controllers\Api\OfficialController@update');
    });

    Route::middleware(['permission:referees.view'])->group(function () {
        Route::get('/referees', 'App\Http\Controllers\Api\RefereeController@index');
        Route::get('/referees/{referee}', 'App\Http\Controllers\Api\RefereeController@show');
    });

    Route::middleware(['permission:referees.create'])->group(function () {
        Route::post('/referees', 'App\Http\Controllers\Api\RefereeController@store');
    });

    Route::middleware(['permission:referees.update'])->group(function () {
        Route::patch('/referees/{referee}', 'App\Http\Controllers\Api\RefereeController@update');
    });

    // ============================
    // TRAINING COURSES
    // ============================
    Route::middleware(['permission:courses.view'])->group(function () {
        Route::get('/courses', 'App\Http\Controllers\Api\TrainingCourseController@index');
        Route::get('/courses/{course}', 'App\Http\Controllers\Api\TrainingCourseController@show');
    });

    Route::middleware(['permission:courses.create'])->group(function () {
        Route::post('/courses', 'App\Http\Controllers\Api\TrainingCourseController@store');
    });

    Route::middleware(['permission:courses.enroll'])->group(function () {
        Route::post('/courses/{course}/enroll', 'App\Http\Controllers\Api\TrainingCourseController@enroll');
    });

    // ============================
    // DISCIPLINARY
    // ============================
    Route::middleware(['permission:disciplinary.view'])->group(function () {
        Route::get('/disciplinary-cases', 'App\Http\Controllers\Api\DisciplinaryCaseController@index');
        Route::get('/disciplinary-cases/{case}', 'App\Http\Controllers\Api\DisciplinaryCaseController@show');
    });

    Route::middleware(['permission:disciplinary.create'])->group(function () {
        Route::post('/disciplinary-cases', 'App\Http\Controllers\Api\DisciplinaryCaseController@store');
    });

    Route::middleware(['permission:disciplinary.manage'])->group(function () {
        Route::post('/disciplinary-cases/{case}/sanctions', 'App\Http\Controllers\Api\DisciplinaryCaseController@addSanction');
    });

    // ============================
    // REGISTRATIONS & AFFILIATIONS
    // ============================
    Route::middleware(['permission:registrations.view'])->group(function () {
        Route::get('/registrations', 'App\Http\Controllers\Api\RegistrationController@index');
        Route::get('/registrations/{registration}', 'App\Http\Controllers\Api\RegistrationController@show');
    });

    Route::middleware(['permission:registrations.approve'])->group(function () {
        Route::post('/registrations/{registration}/approve', 'App\Http\Controllers\Api\RegistrationController@approve');
    });

    Route::middleware(['permission:registrations.reject'])->group(function () {
        Route::post('/registrations/{registration}/reject', 'App\Http\Controllers\Api\RegistrationController@reject');
    });

    // ============================
    // FUNDS
    // ============================
    Route::middleware(['permission:funds.view'])->group(function () {
        Route::get('/funds', 'App\Http\Controllers\Api\FundController@index');
        Route::get('/funds/{fund}', 'App\Http\Controllers\Api\FundController@show');
    });

    Route::middleware(['permission:funds.create'])->group(function () {
        Route::post('/funds', 'App\Http\Controllers\Api\FundController@store');
    });

    Route::middleware(['permission:funds.disburse'])->group(function () {
        Route::post('/funds/{fund}/disburse', 'App\Http\Controllers\Api\FundController@disburse');
    });

    // ============================
    // REGIONS
    // ============================
    Route::get('/regions', 'App\Http\Controllers\Api\RegionController@index');

    // ============================
    // REPORTS
    // ============================
    Route::middleware(['permission:reports.dashboard'])->group(function () {
        Route::get('/reports/dashboard', 'App\Http\Controllers\Api\ReportController@dashboard');
    });

    Route::middleware(['permission:reports.financial'])->group(function () {
        Route::get('/reports/financial', 'App\Http\Controllers\Api\ReportController@financial');
    });

    Route::middleware(['permission:reports.registrations'])->group(function () {
        Route::get('/reports/registrations', 'App\Http\Controllers\Api\ReportController@registrations');
    });

    Route::middleware(['permission:reports.transfers'])->group(function () {
        Route::get('/reports/transfers', 'App\Http\Controllers\Api\ReportController@transfers');
    });

    // ============================
    // FIFA SYNC
    // ============================
    Route::middleware(['permission:fifa.view_status'])->group(function () {
        Route::get('/fifa/sync-status', 'App\Http\Controllers\Api\FifaSyncController@status');
    });

    Route::middleware(['permission:fifa.trigger_sync'])->group(function () {
        Route::post('/fifa/sync/{entity}/{id}', 'App\Http\Controllers\Api\FifaSyncController@sync');
    });

    Route::middleware(['permission:fifa.view_mismatches'])->group(function () {
        Route::get('/fifa/mismatches', 'App\Http\Controllers\Api\FifaSyncController@mismatches');
    });

    // ============================
    // SYSTEM SETTINGS (Super Admin Only)
    // ============================
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/settings', 'App\Http\Controllers\Api\SettingController@index');
        Route::put('/settings', 'App\Http\Controllers\Api\SettingController@update');

        // User management
        Route::get('/users', 'App\Http\Controllers\Api\UserController@index');
        Route::post('/users', 'App\Http\Controllers\Api\UserController@store');
        Route::get('/users/{user}', 'App\Http\Controllers\Api\UserController@show');
        Route::patch('/users/{user}', 'App\Http\Controllers\Api\UserController@update');
        Route::delete('/users/{user}', 'App\Http\Controllers\Api\UserController@destroy');
        Route::post('/users/{user}/roles', 'App\Http\Controllers\Api\UserController@assignRoles');

        // Audit logs
        Route::get('/audit-logs', 'App\Http\Controllers\Api\AuditLogController@index');
    });
});
