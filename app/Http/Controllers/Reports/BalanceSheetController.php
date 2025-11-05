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

class BalanceSheetController extends Controller
{
	public function index(Request $request)
	{
		$asOf = $request->get('as_of', now()->toDateString());

		$accounts = Account::whereIn('type', ['asset', 'liability', 'equity'])
		->with(['entries' => function ($q) use ($asOf) {
			$q->whereHas('journal', function ($j) use ($asOf) {
				$j->where('status', 'posted')
				->where('date', '<=', $asOf);
			});
		}])->get();

		$assets = $accounts->where('type', 'asset');
		$liabilities = $accounts->where('type', 'liability');
		$equity = $accounts->where('type', 'equity');

		$totalAssets = $assets->sum(fn($a) => $a->entries->sum('debit') - $a->entries->sum('credit'));
		$totalLiabilities = $liabilities->sum(fn($a) => $a->entries->sum('credit') - $a->entries->sum('debit'));
		$totalEquity = $equity->sum(fn($a) => $a->entries->sum('credit') - $a->entries->sum('debit'));

		$balance = $totalAssets - ($totalLiabilities + $totalEquity);

		return view('reports.balance-sheet.index', compact(
			'assets', 'liabilities', 'equity',
			'totalAssets', 'totalLiabilities', 'totalEquity',
			'balance', 'asOf'
		));
	}
}
