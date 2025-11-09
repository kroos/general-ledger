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

	public function create()
	{
		return view('accounting.journals.create');
	}

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

		try {
			DB::beginTransaction();

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

			if ($request->has('post_now')) {
				if (!$journal->isBalanced()) {
					throw new \DomainException('Cannot post unbalanced journal. Please ensure total debit equals total credit.');
				}
				$journal->update(['status' => 'posted']);
			} else {
				$journal->update(['status' => 'draft']);
			}

			DB::commit();

			$msg = $journal->status === 'posted'
			? 'Journal posted successfully.'
			: 'Draft journal saved.';

			return redirect()->route('journals.index')->with('success', $msg);
		} catch (\DomainException $e) {
			DB::rollBack();
			return back()->withInput()->with('danger', $e->getMessage());
		} catch (\Throwable $e) {
			DB::rollBack();
			report($e);
			return back()->withInput()->with('danger', 'Unexpected error: ' . $e->getMessage());
		}
	}

	public function post(Journal $journal)
	{
		try {
			JournalService::postDraft($journal);
			return back()->with('success', 'Journal posted successfully.');
		} catch (\DomainException $e) {
			return back()->with('danger', $e->getMessage());
		} catch (\Throwable $e) {
			report($e);
			return back()->with('danger', 'Unexpected error: ' . $e->getMessage());
		}
	}

	public function unpost(Journal $journal)
	{
		try {
			JournalService::unpost($journal);
			return back()->with('success', 'Journal reverted to draft successfully.');
		} catch (\DomainException $e) {
			return back()->with('danger', $e->getMessage());
		} catch (\Throwable $e) {
			report($e);
			return back()->with('danger', 'Unexpected error: ' . $e->getMessage());
		}
	}

	public function destroy(Journal $journal)
	{
		$journal->delete();
		return response()->json(['success' => true]);
	}
}
