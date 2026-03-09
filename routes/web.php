<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChargeController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NaturalPersonController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ResolucionController;
use App\Http\Controllers\LegalEntityController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', [LoginController::class, 'index'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/storage-link', function () {
    $exitCode = Artisan::call('storage:link');
    return response()->json([
        'ok' => $exitCode === 0,
        'exit_code' => $exitCode,
        'output' => Artisan::output(),
    ]);
})->name('storage.link');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::resource('resolucions', ResolucionController::class)->except('create', 'show', 'edit');
    Route::post('resolucions/{resolucion}/charge', [ResolucionController::class, 'createCharge'])
        ->name('resolucions.charge.create');

    Route::get('charges/reports/sent', [ChargeController::class, 'reportSent'])->name('charges.reports.sent');
    Route::get('charges/reports/created', [ChargeController::class, 'reportCreated'])->name('charges.reports.created');
    Route::get('charges/reports/resolution', [ChargeController::class, 'reportResolution'])
        ->name('charges.reports.resolution');
    Route::get('charges/reports/received', [ChargeController::class, 'reportReceived'])->name('charges.reports.received');
    Route::put('charges/{charge}/sign', [ChargeController::class, 'signStore'])->name('charges.sign.store');
    Route::put('charges/{charge}/reject', [ChargeController::class, 'reject'])->name('charges.reject');
    Route::get('charges/refresh', [ChargeController::class, 'refresh'])->name('charges.refresh');
    Route::resource('charges', ChargeController::class)->except('show');

    Route::resource('users', UserController::class)->except('show');
    Route::resource('roles', RoleController::class)->except('show');
    Route::post('natural-people/import', [NaturalPersonController::class, 'import'])->name('natural-people.import');
    Route::resource('natural-people', NaturalPersonController::class)->except('show');
    Route::post('legal-entities/import', [LegalEntityController::class, 'import'])->name('legal-entities.import');
    Route::resource('legal-entities', LegalEntityController::class)->except('show');
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
    Route::post('settings/import', [SettingsController::class, 'import'])->name('settings.import');
    Route::post('settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    Route::post('/import', [ResolucionController::class, 'import'])->name('index.import');

    Route::get('/descargar-plantilla', [ResolucionController::class, 'downloadTemplate'])
        ->name('download.template');

    Route::get('/resoluciones/pdf', [ResolucionController::class, 'generatePDF'])->name('resoluciones.pdf');

    Route::get('/resoluciones/excel', [ResolucionController::class, 'exportExcel'])->name('resoluciones.excel');

    Route::get('/', [HomeController::class, 'index'])->name('home');
});
