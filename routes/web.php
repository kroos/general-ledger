<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Accounting\JournalController;
use App\Http\Controllers\System\ActivityLogController;
use App\Http\Controllers\Reports\GeneralLedgerController;
use App\Http\Controllers\Reports\TrialBalanceController;
use App\Http\Controllers\Reports\ProfitLossController;
use App\Http\Controllers\Reports\BalanceSheetController;
use App\Http\Controllers\Accounting\AccountController;
use App\Http\Controllers\Accounting\SalesInvoiceController;
use App\Http\Controllers\Accounting\PurchaseBillController;
use App\Http\Controllers\Accounting\PaymentController;

Route::get('/', function () {
	return view('welcome');
});

Route::post('/', function (Request $request) {
	// dd($request->all());
	$request->validate([
		'skills' => 'required|array|min:1',
		'experiences' => 'required|array|min:1',
		'countries' => 'required|array|min:1',
		'skills.*.name' => 'required',
		'skills.*.skill' => 'required',
		'skills.*.subskills.*.subskill' => 'required',
		'skills.*.subskills.*.years' => 'required',
		'experiences.*.name' => 'required',
		'experiences.*.id' => 'required',
		'countries.*.country_id' => 'required',
		'countries.*.state_id' => 'required',
	], [
		'skills.required' => 'Please add at least one skill.',
		'experiences.required' => 'Please add at least one experience.',
		'countries.required' => 'Please add at least one country',
			'skills.*.name' => 'Please insert :attribute at #:position',   //:index
			'skills.*.skill' => 'Please insert :attribute at #:position',   //:index
			'skills.*.subskills.*.subskill' => 'Please insert :attribute at #:position',   //:index
			'skills.*.subskills.*.years' => 'Please insert :attribute at #:position',   //:index
			'experiences.*.name' => 'Please insert :attribute at #:position',   //:index
			'experiences.*.id' => 'Please insert :attribute at #:position',   //:index
			'countries.*.country_id' => 'Please insert :attribute at #:position',
			'countries.*.state_id' => 'Please insert :attribute at #:position',
		], [
			'countries.*.country_id' => 'Country',
			'countries.*.state_id' => 'State',
			'skills.*.name' => 'Name',
			'skills.*.skill' => 'Skill',
			'skills.*.subskills.*.subskill' => 'Sub-Skill',
			'skills.*.subskills.*.years' => 'Years',
			'experiences.*.name' => 'Name',
			'experiences.*.id' => 'ID',
		]);
	return redirect()->back()->with('success', 'Successfully submitted form');
})->name('welcome');

Route::get('/dashboard', function () {
	return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
Route::middleware(['auth', 'password.confirm'])->group(function () {
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

/////////////////////////////////////////////////////////////////////////////////////////////////////

Route::prefix('journals')->name('journals.')->group(function () {
	Route::get('/', [JournalController::class, 'index'])->name('index');
	Route::get('/create', [JournalController::class, 'create'])->name('create');
	Route::post('/', [JournalController::class, 'store'])->name('store');
	Route::post('/{journal}/post', [JournalController::class, 'post'])->name('post');
	Route::post('/{journal}/unpost', [JournalController::class, 'unpost'])->name('unpost');
	Route::delete('/{journal}', [JournalController::class, 'destroy'])->name('destroy');
});

Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
	Route::get('/', [ActivityLogController::class, 'index'])->name('index');
	Route::get('/{log}', [ActivityLogController::class, 'show'])->name('show');
	Route::delete('/{log}', [ActivityLogController::class, 'destroy'])->name('destroy');
});

Route::get('/journals/draft-count', function () {
	$count = \App\Models\Accounting\Journal::where('status','draft')->count();
	return response()->json(['count'=>$count]);
})->name('journals.draft-count');

Route::prefix('reports')->name('reports.')->group(function () {
	Route::get('/general-ledger', [GeneralLedgerController::class, 'index'])->name('general-ledger.index');
	Route::get('trial-balance', [TrialBalanceController::class, 'index'])->name('trial-balance.index');
	Route::get('profit-loss', [ProfitLossController::class, 'index'])->name('profit-loss.index');
	Route::get('balance-sheet', [BalanceSheetController::class, 'index'])->name('balance-sheet.index');
});

Route::prefix('accounts')->name('accounts.')->group(function () {
	Route::get('/', [AccountController::class, 'index'])->name('index');
	Route::get('/create', [AccountController::class, 'create'])->name('create');
	Route::post('/', [AccountController::class, 'store'])->name('store');
	Route::get('/{account}/edit', [AccountController::class, 'edit'])->name('edit');
	Route::patch('/{account}', [AccountController::class, 'update'])->name('update');
	Route::delete('/{account}', [AccountController::class, 'destroy'])->name('destroy');
});

Route::prefix('accounting')->name('accounting.')->group(function () {
	Route::resource('sales-invoices', SalesInvoiceController::class);
	Route::post('sales-invoices/{invoice}/post', [SalesInvoiceController::class, 'post'])->name('sales-invoices.post');

	Route::resource('purchase-bills', PurchaseBillController::class);
	Route::post('purchase-bills/{bill}/post', [PurchaseBillController::class, 'post'])->name('purchase-bills.post');
});


Route::resource('payments', PaymentController::class)->names('accounting.payments');

Route::get('journals/draft-count', function() {
	return response()->json(['count' => \App\Models\Accounting\Journal::where('status', 'draft')->count()]);
})->name('journals.draft-count');


