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
use App\Services\Support\DataTableResponse;

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
	/** Journal list page (DataTables AJAX) */
	public function index(Request $request)
	{
		$query = Journal::with('ledgerType')
		->select(['id','date','reference_no','ledger_type_id','status','description']);

		if ($response = DataTableResponse::from($request, $query, ['id','date','reference_no','status','description'], function($j) {
			return [
				'id' => $j->id,
				'date' => $j->date?->format('Y-m-d'),
				'reference_no' => $j->reference_no ?? '-',
				'ledger' => $j->ledgerType?->name ?? '-',
				'status' => ucfirst($j->status),
				'description' => $j->description ?? '-',
				'action' => view('accounting.journals._actions', compact('j'))->render(),
			];
		})) {
			return $response;
		}

		return view('accounting.journals.index');
	}

	/** Show create form */
	public function create()
	{
		$ledgerTypes = LedgerType::pluck('name', 'id');
		$accounts = Account::orderBy('code')->pluck('name','id');

		return view('accounting.journals.create', compact('ledgerTypes','accounts'));
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

						// Draft-aware posting
			if ($request->has('post_now')) {
				if (!$journal->isBalanced()) {
					return back()->withErrors(['msg' => 'Cannot post unbalanced journal.']);
				}
				$journal->update(['status' => 'posted']);
			}

			DB::commit();

			return redirect()->route('journals.index')
			->with('success', $journal->status === 'posted'
						 ? 'Journal posted successfully.'
						 : 'Draft journal saved.');
		} catch (\Throwable $e) {
			DB::rollBack();
			report($e);
			return back()->withErrors(['message' => $e->getMessage()]);
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
