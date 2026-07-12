<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Catalogs\AsuntoTypeController;
use App\Http\Controllers\Catalogs\LegalEntityController;
use App\Http\Controllers\Catalogs\LevelModalityController;
use App\Http\Controllers\Catalogs\NaturalPersonController;
use App\Http\Controllers\Catalogs\ResolucionTypeController;
use App\Http\Controllers\Operations\ChargeController;
use App\Http\Controllers\Operations\ResolucionController;
use App\Http\Controllers\System\ActivityLogController;
use App\Http\Controllers\System\HomeController;
use App\Http\Controllers\System\LookupController;
use App\Http\Controllers\System\RoleController;
use App\Http\Controllers\System\SearchController;
use App\Http\Controllers\System\SettingsController;
use App\Http\Controllers\System\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'index'])->name('login.form');
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::get('/storage-link', function () {
    $exitCode = Artisan::call('storage:link');

    return response()->json(['ok' => $exitCode === 0, 'exit_code' => $exitCode, 'output' => Artisan::output()]);
})->name('storage.link');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Módulo de Resoluciones (Protegido por permiso)
    Route::middleware(['can:modulo resoluciones'])->group(function () {
        Route::resource('resolucions', ResolucionController::class)->except('create', 'show', 'edit');
        Route::post('resolucions/{resolucion}/charge', [ResolucionController::class, 'createCharge'])
            ->name('resolucions.charge.create');
        Route::get('charges/reports/resolution', [ChargeController::class, 'reportResolution'])
            ->name('charges.reports.resolution');
        Route::post('/import', [ResolucionController::class, 'import'])->name('index.import');
        Route::get('/resoluciones/pdf', [ResolucionController::class, 'generatePDF'])->name('resoluciones.pdf');
        Route::get('/resoluciones/excel', [ResolucionController::class, 'exportExcel'])->name('resoluciones.excel');
        Route::get('resolucions/{resolucion}/document', [ResolucionController::class, 'getDocument'])->name('resolucions.file.document');
        Route::patch('resolucions/{resolucion}/work', [ResolucionController::class, 'markAsWorked'])->name('resolucions.work');
    });

    // Módulo de Cargos
    Route::get('charges/reports/sent', [ChargeController::class, 'reportSent'])->name('charges.reports.sent');
    Route::get('charges/reports/created', [ChargeController::class, 'reportCreated'])->name('charges.reports.created');
    Route::get('charges/reports/received', [ChargeController::class, 'reportReceived'])->name('charges.reports.received');
    Route::put('charges/{charge}/sign', [ChargeController::class, 'signStore'])->name('charges.sign.store');
    Route::put('charges/{charge}/reject', [ChargeController::class, 'reject'])->name('charges.reject');
    Route::get('charges/refresh', [ChargeController::class, 'refresh'])->name('charges.refresh');
    Route::get('notifications/pending-charges', [ChargeController::class, 'pendingNotifications'])->name('notifications.pending-charges');
    Route::post('notifications/{id}/read', [ChargeController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::get('charges/{charge}/signature', [ChargeController::class, 'getSignature'])->name('charges.file.signature');
    Route::get('charges/{charge}/evidence', [ChargeController::class, 'getEvidence'])->name('charges.file.evidence');
    Route::get('charges/{charge}/document', [ChargeController::class, 'getDocument'])->name('charges.file.document');
    Route::get('charges/{charge}/carta-poder', [ChargeController::class, 'getCartaPoder'])->name('charges.file.carta-poder');
    Route::resource('charges', ChargeController::class)->except('show');

    // Otros recursos
    Route::resource('users', UserController::class)->except('show');
    Route::resource('roles', RoleController::class)->except('show');
    Route::post('natural-people/import', [NaturalPersonController::class, 'import'])->name('natural-people.import');
    Route::get('natural-people/download-template', [NaturalPersonController::class, 'downloadTemplate'])->name('natural-people.download-template');
    Route::resource('natural-people', NaturalPersonController::class)->except('show');
    Route::post('legal-entities/import', [LegalEntityController::class, 'import'])->name('legal-entities.import');
    Route::get('legal-entities/download-template', [LegalEntityController::class, 'downloadTemplate'])->name('legal-entities.download-template');
    Route::resource('legal-entities', LegalEntityController::class)->except('show');

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
    Route::post('settings/import', [SettingsController::class, 'import'])->name('settings.import');
    Route::post('settings/reset', [SettingsController::class, 'reset'])->name('settings.reset');

    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::resource('resolucion-types', ResolucionTypeController::class)->except('create', 'show', 'edit');
    Route::resource('asunto-types', AsuntoTypeController::class)->except('create', 'show', 'edit');
    Route::resource('level-modalities', LevelModalityController::class)->except('create', 'show', 'edit');
    Route::get('/descargar-plantilla', [ResolucionController::class, 'downloadTemplate'])->name('download.template');
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Rutas para búsquedas dinámicas (Select2 / AJAX / APIs Externas)
    Route::prefix('search')->group(function () {
        Route::get('natural-people', [SearchController::class, 'naturalPeople'])->name('search.natural-people');
        Route::get('natural-people/by-dni/{dni}', [LookupController::class, 'naturalPersonByDni'])->name('search.by-dni');
        Route::get('natural-people/by-cedula/{cedula}', [SearchController::class, 'byCedula'])->name('search.by-cedula');
        Route::get('legal-entities', [SearchController::class, 'legalEntities'])->name('search.legal-entities');
        Route::get('legal-entities/by-ruc/{ruc}', [LookupController::class, 'legalEntityByRuc'])->name('search.by-ruc');
        Route::get('users', [SearchController::class, 'users'])->name('search.users');
        Route::get('pending-resolutions', [SearchController::class, 'pendingResolutions'])->name('search.pending-resolutions');
        Route::get('asuntos-by-resolution-type/{id}', [SearchController::class, 'asuntosByResolutionType'])->name('search.asuntos-by-type');
    });
});
