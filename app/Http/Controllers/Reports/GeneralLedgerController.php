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
use App\Models\Accounting\{JournalEntry, Account};

// load db facade
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

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

class GeneralLedgerController extends Controller
{
	public function index(Request $request)
	{
		$accounts = Account::orderBy('code')->get();

		if ($request->ajax()) {
			$accountId = $request->account_id;
			$entries = JournalEntry::whereHas('journal', fn($q) => $q->where('status', 'posted'))
			->where('account_id', $accountId)
			->orderBy('id')
			->with('journal')
			->get(['id','journal_id','debit','credit','description']);

			$running = 0;
			$data = [];
			foreach ($entries as $entry) {
				$running += ($entry->debit - $entry->credit);
				$data[] = [
					'date' => $entry->journal->date->format('Y-m-d'),
					'journal_id' => $entry->journal_id,
					'desc' => $entry->description ?? $entry->journal->description,
					'debit' => number_format($entry->debit, 2),
					'credit' => number_format($entry->credit, 2),
					'balance' => number_format($running, 2),
				];
			}

			return response()->json(['data' => $data]);
		}

		return view('reports.general-ledger.index', compact('accounts'));
	}
}
