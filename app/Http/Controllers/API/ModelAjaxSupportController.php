<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

// for controller output
use Illuminate\Http\JsonResponse;
// use Illuminate\Http\RedirectResponse;
// use Illuminate\Support\Facades\Redirect;
// use Illuminate\Http\Response;
// use Illuminate\View\View;

// models
use App\Models\{
	ActivityLog
};
use App\Models\Accounting\{
	Account, AccountType, Ledger, Journal, JournalEntry
};

// load db facade
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

// load validation
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use {{ namespacedRequests }}

// load batch and queue
// use Illuminate\Bus\Batch;
// use Illuminate\Support\Facades\Bus;

// load email & notification
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;// more email

// load pdf
// use Barryvdh\DomPDF\Facade\Pdf;

// load helper
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

// load Carbon library
use \Carbon\Carbon;
use \Carbon\CarbonPeriod;
use \Carbon\CarbonInterval;

use Session;
use Throwable;
use Exception;
use Log;

class ModelAjaxSupportController extends Controller
{
	public function getActivityLogs(Request $request): JsonResponse
	{
		$values = ActivityLog::with('belongstouser')
											->when($request->search, function(Builder $query) use ($request){
												$query->where('model_type','LIKE','%'.$request->search.'%')
												->orWhereHas('belongstouser', function ($q2) use ($request) {
													$q2->where('name', 'LIKE', '%' . $request->search . '%');
												});
											})
											->when($request->id, function($query) use ($request){
												$query->where('id', $request->id);
											})
											->orderBy('created_at', 'DESC')
											->get();
		return response()->json($values);
	}

	public function getAccounts(Request $request): JsonResponse
	{
		$accounts = Account::with('belongstoaccounttype')
												->when($request->search, function ($query) use ($request) {
													$query->where(function ($q) use ($request) {
														$q->where('account', 'LIKE', '%' . $request->search . '%')
														->orWhere('code','LIKE','%'.$request->search.'%')
														->orWhereHas('belongstoaccounttype', function ($q2) use ($request) {
															$q2->where('account_type', 'LIKE', '%' . $request->search . '%');
														});
													});
												})
												->when($request->id, function($query) use ($request){
													$query->where('id', $request->id);
												})
												->get();
		return response()->json($accounts);
	}

	public function getAccountTypes(Request $request): JsonResponse
	{
		$accounttypes = AccountType::with('hasmanyaccount')
																->when($request->search, function (Builder $query) use ($request) {
																	$query->where('account_type', 'LIKE', '%' . $request->search . '%');
																})
																->when($request->id, function($query) use ($request){
																	$query->where('id', $request->id);
																})
																->get();
		return response()->json($accounttypes);
	}

	public function getLedgers(Request $request): JsonResponse
	{
		$ledgers = Ledger::with(['belongstoaccounttype', 'hasmanyjournal'])
												->when($request->search, function ($query) use ($request) {
													$query->where(function ($q) use ($request) {
														$q->where('ledger', 'LIKE', '%' . $request->search . '%')
														->orWhereHas('belongstoaccounttype', function ($q2) use ($request) {
															$q2->where('account_type', 'LIKE', '%' . $request->search . '%');
														});
													});
												})
												->when($request->id, function($query) use ($request){
													$query->where('id', $request->id);
												})
												->get();
		return response()->json($ledgers);
	}

	public function getJournals(Request $request): JsonResponse
	{
		$journals = Journal::with(['belongstoledger', 'hasmanyjournalentries'])
												->when($request->search, function (Builder $query) use ($request) {
													$query->WhereHas('belongstoledger', function ($q2) use ($request) {
														$q2->where('ledger', 'LIKE', '%' . $request->search . '%');
													});
												})
												->when($request->id, function($query) use ($request){
													$query->where('id', $request->id);
												})
												->get()->toArray();
		return response()->json($journals);
	}

	public function getJournalEntries(Request $request): JsonResponse
	{
		$journalentries = JournalEntry::with(['belongstojournal', 'belongstoaccount', 'belongstoledger'])
												->when($request->search, function (Builder $query) use ($request) {
													$query->where('no_reference', 'LIKE', '%' . $request->search . '%')
														->orWhere('description','LIKE','%'.$request->search.'%');
												})
												->when($request->id, function($query) use ($request){
													$query->where('id', $request->id);
												})
												->get();
		return response()->json($journalentries);
	}

	public function getGeneralLedgerReport(Request $request)
	{
		$accountId = $request->account_id;
		$entries = JournalEntry::where('account_id', $accountId)
		->orderBy('id')
		->with(['belongstojournal', 'belongstoaccount', 'belongstoledger'])
		->get();

		$running = 0;
		$data = [];
		foreach ($entries as $entry) {
			$running += ($entry->debit - $entry->credit);
			$data[] = [
				'date' => $entry->belongstojournal->date->format('j M Y'),
				'journal_id' => $entry->journal_id,
				'description' => $entry->description ?? $entry->belongstojournal->description,
				'debit' => number_format($entry->debit, 2),
				'credit' => number_format($entry->credit, 2),
				'balance' => number_format($running, 2),
			];
		}
		return response()->json($data);
	}

	public function getTrialBalanceReport(Request $request)
	{
		$from = $request->from;
		$to = $request->to;

		$accounts = Account::orderBy('code')
		->with(['hasmanyjournalentries' => function ($query) use ($from, $to) {
			$query->whereBetween('date', [$from, $to]);
		}])
		->get();

		$rows = $accounts->map(function ($acc) {
			$debit = $acc->hasmanyjournalentries->sum('debit');
			$credit = $acc->hasmanyjournalentries->sum('credit');
			$balance = $debit - $credit;

			return [
				'account' => $acc->code . ' - ' . $acc->account,
				'debit' => number_format($debit, 2),
				'credit' => number_format($credit, 2),
				'balance' => number_format($balance, 2),
				'type' => $balance >= 0 ? 'Debit' : 'Credit',
			];
		});

		$totalDebit = $accounts->flatMap->hasmanyjournalentries->sum('debit');
		$totalCredit = $accounts->flatMap->hasmanyjournalentries->sum('credit');

    // DataTables expected JSON format
		return response()->json([
			'data' => $rows,
			'totals' => [
				'totalDebit' => number_format($totalDebit, 2),
				'totalCredit' => number_format($totalCredit, 2),
			],
		]);
	}

	public function getBalanceSheetReport(Request $request)
	{
		$asOf = $request->get('as_of');

		$accounts = Account::whereIn('account_type_id', [1,2,3,4])
												->with(['hasmanyjournalentries' => function ($q) use ($asOf) {
													$q->where('date', '<=', $asOf);
												}])
												->get();

		$assets = $accounts->whereIn('account_type_id', [1,2]);
		$liabilities = $accounts->where('account_type_id', 4);
		$equity = $accounts->where('account_type_id', 3);

    // Prepare rows as numeric arrays
		$assetsRows = $assets->map(fn($a) => [
			'account' => $a->account,
			'amount' => $a->hasmanyjournalentries->sum('debit') - $a->hasmanyjournalentries->sum('credit')
    ])->values()->all(); // <- ensure numeric array

		$liabilitiesRows = $liabilities->map(fn($a) => [
			'account' => $a->account,
			'amount' => $a->hasmanyjournalentries->sum('credit') - $a->hasmanyjournalentries->sum('debit')
    ])->values()->all(); // <- ensure numeric array

		$equityRows = $equity->map(fn($a) => [
			'account' => $a->account,
			'amount' => $a->hasmanyjournalentries->sum('credit') - $a->hasmanyjournalentries->sum('debit')
    ])->values()->all(); // <- ensure numeric array

    // Totals
		$totalAssets = $assets->sum(fn($a) => $a->hasmanyjournalentries->sum('debit') - $a->hasmanyjournalentries->sum('credit'));
		$totalLiabilities = $liabilities->sum(fn($a) => $a->hasmanyjournalentries->sum('credit') - $a->hasmanyjournalentries->sum('debit'));
		$totalEquity = $equity->sum(fn($a) => $a->hasmanyjournalentries->sum('credit') - $a->hasmanyjournalentries->sum('debit'));
		$balance = $totalAssets - ($totalLiabilities + $totalEquity);

		return response()->json([
			'assets' => $assetsRows,
			'liabilities' => $liabilitiesRows,
			'equity' => $equityRows,
			'totals' => [
				'totalAssets' => $totalAssets,
				'totalLiabilities' => $totalLiabilities,
				'totalEquity' => $totalEquity,
				'balance' => $balance,
			],
		]);
	}

	public function getProfitLossReport(Request $request)
	{
		$from = $request->from;
		$to = $request->to;

		$accounts = Account::whereIn('account_type_id', [5, 6])		// grap only income and expense
													->with(['hasmanyjournalentries' => function($q) use ($from, $to){
														$q->whereBetween('date', [$from, $to]);
													}])
													->get();
													// ->ddrawsql();
													// ->dd();

		$incomeAccounts  = $accounts->where('account_type_id', 5);
		$expenseAccounts = $accounts->where('account_type_id', 6);

		$incomesRows = $incomeAccounts->map(fn($a) => [
																										'account' => $a->account,
																										'amount' => $a->hasmanyjournalentries->sum('debit') - $a->hasmanyjournalentries->sum('credit')
																							    ])
																									// ->dd();
																							    ->values()
																							    ->all(); // <- ensure numeric array

		$expensesRows = $expenseAccounts->map(fn($a) => [
																									'account' => $a->account,
																									'amount' => $a->hasmanyjournalentries->sum('debit') - $a->hasmanyjournalentries->sum('credit')
																						    ])
																						    ->values()
																						    ->all(); // <- ensure numeric array


		$totalIncome = $incomeAccounts->sum(fn($a) => $a->hasmanyjournalentries->sum('credit') - $a->hasmanyjournalentries->sum('debit'));
		$totalExpense = $expenseAccounts->sum(fn($a) => $a->hasmanyjournalentries->sum('debit') - $a->hasmanyjournalentries->sum('credit'));

		$netProfit = $totalIncome - $totalExpense;

		return response()->json([
			'incomesRows' => $incomesRows,
			'expensesRows' => $expensesRows,
			'totalIncome' => $totalIncome,
			'totalExpense' => $totalExpense,
			'netProfit' => $netProfit,
		]);
	}


}

