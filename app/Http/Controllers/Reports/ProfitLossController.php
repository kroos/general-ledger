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

class ProfitLossController extends Controller
{
	public function index(Request $request)
	{
		// $from = $request->from;
		// $to = $request->to;

		// $accounts = Account::whereIn('account_type_id', [5, 6])		// grap only income and expense
		// 											->with(['hasmanyjournalentries' => function($q) use ($from, $to){
		// 												$q->whereBetween('date', [$from, $to]);
		// 											}])
		// 											->get();

		// $incomeAccounts  = $accounts->where('account_type_id', 5);
		// $expenseAccounts = $accounts->where('account_type_id', 6);

		// $totalIncome = $incomeAccounts->sum(fn($a) => $a->hasmanyjournalentries->sum('credit') - $a->hasmanyjournalentries->sum('debit'));
		// $totalExpense = $expenseAccounts->sum(fn($a) => $a->hasmanyjournalentries->sum('debit') - $a->hasmanyjournalentries->sum('credit'));

		// $netProfit = $totalIncome - $totalExpense;

		return view('reports.profit-loss.index');
	}


}
