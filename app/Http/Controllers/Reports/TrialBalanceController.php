<?php
namespace App\Http\Controllers\Reports;
use App\Http\Controllers\Controller;

// for controller output
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Response;
use Illuminate\View\View;

// models
use App\Models\Accounting\Account;
use App\Models\Accounting\JournalEntry;

// load db facade
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

// load validation
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use {{ namespacedRequests }}

// load batch and queue
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

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

class TrialBalanceController extends Controller
{
	public function index(Request $request)
	{
		$from = $request->get('from', now()->startOfMonth()->toDateString());
		$to = $request->get('to', now()->toDateString());

		$accounts = Account::orderBy('code')
												->with(['hasmanyjournalentries' => function ($query1) use ($from, $to) {
														$query1->whereBetween('date', [$from, $to]);
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

		return view('reports.trial-balance.index', compact('rows', 'totalDebit', 'totalCredit', 'from', 'to'));
	}
}
