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
		$from = $request->get('from', now()->startOfMonth()->toDateString());
		$to = $request->get('to', now()->toDateString());

		// Fetch only Income & Expense accounts
		$accounts = Account::whereIn('type', ['income', 'expense'])
		->with(['entries' => function ($q) use ($from, $to) {
			$q->whereHas('journal', function ($j) use ($from, $to) {
				$j->whereBetween('date', [$from, $to])
				->where('status', 'posted');
			});
		}])->get();

		$incomeAccounts = $accounts->where('type', 'income');
		$expenseAccounts = $accounts->where('type', 'expense');

		$totalIncome = $incomeAccounts->sum(fn($a) => $a->entries->sum('credit') - $a->entries->sum('debit'));
		$totalExpense = $expenseAccounts->sum(fn($a) => $a->entries->sum('debit') - $a->entries->sum('credit'));

		$netProfit = $totalIncome - $totalExpense;

		return view('reports.profit-loss.index', compact(
			'incomeAccounts', 'expenseAccounts', 'totalIncome', 'totalExpense', 'netProfit', 'from', 'to'
		));
	}
}
