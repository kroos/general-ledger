<?php
use Illuminate\Support\Facades\Route;

// read API from files
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\API\ModelAjaxSupportController;

Route::middleware(['auth', 'auth:sanctum'])->group(function () {
	Route::controller(ModelAjaxSupportController::class)->group(function () {
		Route::get('/getActivityLogs', 'getActivityLogs')->name('getActivityLogs');
		Route::get('/getYesNoOptions', 'getYesNoOptions')->name('getYesNoOptions');
		Route::get('/getAccounts', 'getAccounts')->name('getAccounts');
		Route::get('/getAccountTypes', 'getAccountTypes')->name('getAccountTypes');
		Route::get('/getLedgers', 'getLedgers')->name('getLedgers');
		Route::get('/getJournals', 'getJournals')->name('getJournals');
		Route::get('/getJournalEntries', 'getJournalEntries')->name('getJournalEntries');
		Route::get('/getGeneralLedgerReport', 'getGeneralLedgerReport')->name('getGeneralLedgerReport');
		Route::get('/getTrialBalanceReport', 'getTrialBalanceReport')->name('getTrialBalanceReport');
		Route::get('/getProfitLossReport', 'getProfitLossReport')->name('getProfitLossReport');
		Route::get('/getBalanceSheetReport', 'getBalanceSheetReport')->name('getBalanceSheetReport');
	});
});

