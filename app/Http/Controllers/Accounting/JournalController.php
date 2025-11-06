<?php
namespace App\Http\Controllers\Accounting;
use App\Http\Controllers\Controller;

// for controller output
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Response;
use Illuminate\View\View;

// models
use App\Models\Accounting\{Journal, LedgerType, Account};

use App\Services\Accounting\JournalService;
// use App\Services\Support\DataTableResponse;

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

class JournalController extends Controller
{
	public function index(Request $request)
	{
		return view('accounting.journals.index');
	}

	/** Show create form */
	public function create()
	{
		return view('accounting.journals.create');
	}

	/** Store draft or post journal manually */
	public function store(Request $request)
	{
		$data = $request->validate([
			'date' => 'required|date',
			'ledger_type_id' => 'required|exists:ledger_types,id',
			'description' => 'nullable|string',
			'entries' => 'required|array|min:1',
			'entries.*.account_id' => 'required|exists:accounts,id',
			'entries.*.debit' => 'nullable|numeric',
			'entries.*.credit' => 'nullable|numeric',
			'entries.*.description' => 'nullable|string',
		]);

		DB::beginTransaction();

		try {
			$journal = Journal::create([
				'date' => $data['date'],
				'ledger_type_id' => $data['ledger_type_id'],
				'description' => $data['description'] ?? null,
				'status' => 'draft',
			]);

			foreach ($data['entries'] as $line) {
				$journal->entries()->create([
					'account_id' => $line['account_id'],
					'debit' => $line['debit'] ?? 0,
					'credit' => $line['credit'] ?? 0,
					'description' => $line['description'] ?? null,
				]);
			}

			$totalDebit = $journal->entries->sum('debit');
			$totalCredit = $journal->entries->sum('credit');

			if ($request->has('post_now')) {
				if (!$journal->isBalanced()) {
					DB::rollBack();
					return back()
					->withInput()
					->withErrors(['msg' => 'Cannot post unbalanced journal. Please ensure total debit equals total credit.']);
				}
				$journal->update(['status' => 'posted']);
			} else {
				if (bccomp($totalDebit, $totalCredit, 2) !== 0) {
                // Optional: warn user but still save as draft
					session()->flash('status', 'Draft saved but journal is unbalanced.');
				}
			}

			DB::commit();

			return redirect()->route('journals.index')->with('success',
			                                                 $journal->status === 'posted'
			                                                 ? 'Journal posted successfully.'
			                                                 : 'Draft journal saved.'
			                                               );

		} catch (\Throwable $e) {
			DB::rollBack();
			report($e);
			return back()->withInput()->withErrors(['msg' => $e->getMessage()]);
		}
	}


	/** Post existing draft */
	public function post(Journal $journal)
	{
		try {
			\App\Services\Accounting\JournalService::postDraft($journal);
			return back()->with('success', 'Journal posted successfully.');
		} catch (\DomainException $e) {
			return back()->withErrors(['msg' => $e->getMessage()]);
		} catch (\Throwable $e) {
			report($e);
			return back()->withErrors(['msg' => 'Unexpected error: '.$e->getMessage()]);
		}
	}

	public function unpost(Journal $journal)
	{
		try {
			\App\Services\Accounting\JournalService::unpost($journal);
			return back()->with('success', 'Journal reverted to draft successfully.');
		} catch (\DomainException $e) {
			return back()->withErrors(['msg' => $e->getMessage()]);
		} catch (\Throwable $e) {
			report($e);
			return back()->withErrors(['msg' => 'Unexpected error: '.$e->getMessage()]);
		}
	}

	/** Soft delete journal */
	public function destroy(Journal $journal)
	{
		$journal->delete();
		return response()->json(['success' => true]);
	}
}
