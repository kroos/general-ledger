<?php
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Accounting\AccountTypeController;
use App\Http\Controllers\Accounting\AccountController;
use App\Http\Controllers\Accounting\LedgerController;
use App\Http\Controllers\Accounting\JournalController;

use App\Http\Controllers\Reports\GeneralLedgerController;
use App\Http\Controllers\Reports\TrialBalanceController;
use App\Http\Controllers\Reports\ProfitLossController;
use App\Http\Controllers\Reports\BalanceSheetController;

use App\Http\Controllers\System\ActivityLogController;


use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
	Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
	Route::post('register', [RegisteredUserController::class, 'store']);
	Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
	Route::post('login', [AuthenticatedSessionController::class, 'store']);
	Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
	Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
	Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
	Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
	Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
	Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
	Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
	Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
	Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
	Route::put('password', [PasswordController::class, 'update'])->name('password.update');
	Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});



Route::middleware(['auth', 'verified'])->group(function () {
	Route::get('/dashboard', function(){
		return view('dashboard');
	})->name('dashboard');

	Route::middleware('password.confirm')->group(function () {
		Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
		Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
		Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

		Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
			Route::get('/', [ActivityLogController::class, 'index'])->name('index');
			Route::get('/{log}', [ActivityLogController::class, 'show'])->name('show');
			Route::delete('/{log}', [ActivityLogController::class, 'destroy'])->name('destroy');
		})/*->middleware(['auth', 'role:owner'])*/;

	});

	Route::prefix('reports')->name('reports.')->group(function () {
		Route::get('/general-ledger', [GeneralLedgerController::class, 'index'])->name('general-ledger.index');
		Route::get('trial-balance', [TrialBalanceController::class, 'index'])->name('trial-balance.index');
		Route::get('profit-loss', [ProfitLossController::class, 'index'])->name('profit-loss.index');
		Route::get('balance-sheet', [BalanceSheetController::class, 'index'])->name('balance-sheet.index');
	});

	Route::resources([
		'account-types' => AccountTypeController::class,
		'accounts' => AccountController::class,
		'ledgers' => LedgerController::class,
		'journals' => JournalController::class,
	]);





});





// Route::prefix('/account-type')->name('account-type.')->group(function () {
// 	Route::get('/', [\App\Http\Controllers\Accounting\AccountTypeController::class, 'index'])->name('index');
// 	Route::get('/create', [\App\Http\Controllers\Accounting\AccountTypeController::class, 'create'])->name('create');
// 	Route::post('/', [\App\Http\Controllers\Accounting\AccountTypeController::class, 'store'])->name('store');
// 	Route::get('/{accountType}', [\App\Http\Controllers\Accounting\AccountTypeController::class, 'show'])->name('show');
// 	Route::get('/{accountType}', [\App\Http\Controllers\Accounting\AccountTypeController::class, 'show'])->name('show');
// 	Route::get('/{accountType}/edit', [\App\Http\Controllers\Accounting\AccountTypeController::class, 'edit'])->name('edit');
// 	Route::patch('/{accountType}', [\App\Http\Controllers\Accounting\AccountTypeController::class, 'update'])->name('update');
// 	Route::delete('/{accountType}', [\App\Http\Controllers\Accounting\AccountTypeController::class, 'destroy'])->name('destroy');
// });
// Route::resource('journals', App\Http\Controllers\Accounting\JournalController::class);
