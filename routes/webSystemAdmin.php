<?php
use App\Http\Controllers\SystemAdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// System admin routes (protected)
Route::middleware(['auth', 'system_admin'])->prefix('system-admin')->group(function () {
	Route::get('/dashboard', [SystemAdminController::class, 'dashboard'])->name('system.admin.dashboard');
	Route::get('/companies', [SystemAdminController::class, 'companies'])->name('system.admin.companies');
	Route::get('/users', [SystemAdminController::class, 'users'])->name('system.admin.users');
	Route::get('/system-logs', [SystemAdminController::class, 'systemLogs'])->name('system.admin.logs');
	Route::get('/settings', [SystemAdminController::class, 'settings'])->name('system.admin.settings');

	// Impersonate company (for support)
	Route::post('/impersonate/{company}', [SystemAdminController::class, 'impersonateCompany'])->name('system.admin.impersonate');
	Route::post('/stop-impersonating', [SystemAdminController::class, 'stopImpersonating'])->name('system.admin.stop_impersonating');
});




