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
use App\Models\Accounting\Journal;

// load db facade
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;


// load validation
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
	/**
	 * Display a listing of the resource.
	 */
	public function index(): View
	{
		return view('accounting.journals.index');
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): View
	{
		return view('accounting.journals.create');
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request): RedirectResponse
	{
		// dd($request->all());
		$request->validate([
								'ledger_id' => 'required',
								'date' => 'required|date_format:Y-m-d',
								'no_reference' => 'nullable|string',
								'description' => 'nullable|string',
								'journals' => 'required|array|min:1',
								'journals.*.id' => 'nullable',
								'journals.*.date' => 'required|date_format:Y-m-d',
								'journals.*.account_id' => 'required',
								'journals.*.description' => 'nullable|string',
								'journals.*.no_reference' => 'nullable|string',
								'journals.*.ledger_id' => 'required_without:journals.*.ledger_credit_id|nullable',
								'journals.*.debit' => 'required_without:journals.*.credit|nullable',
								'journals.*.credit' => 'required_without:journals.*.debit|nullable',
							],[],[
								'ledger_id' => 'Ledger',
								'date' => 'Date',
								'no_reference' => 'No Reference',
								'description' => 'Description',
								'journals' => 'Journals',
								'journals.*.id' => '',
								'journals.*.date' => 'Journal Date',
								'journals.*.account_id' => 'Journal Account',
								'journals.*.description' => 'Journal Description',
								'journals.*.no_reference' => 'Journal No Reference',
								'journals.*.ledger_id' => 'Journal Ledger',
								'journals.*.debit' => 'Journal Debit',
								'journals.*.credit' => 'Journal Credit',
							]);
		$led = Journal::create($request->only(['ledger_id', 'date', 'no_reference', 'description']));
		foreach ($request->journals ?? [] as $journal) {
			$led->hasmanyjournalentries()->create($journal);
		}

		return redirect()->route('journals.index')->with('success', 'Data Save');
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Journal $journal): View
	{
		return view('accounting.journals.show', ['journal' => $journal]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Journal $journal): View
	{
		return view('accounting.journals.edit', ['journal' => $journal]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Journal $journal): RedirectResponse
	{
		// dd($request->all());
		$request->validate([
												'ledger_id' => 'required',
												'date' => 'required|date_format:Y-m-d',
												'no_reference' => 'nullable|string',
												'description' => 'nullable|string',
												'journals' => 'required|array|min:1',
												'journals.*.id' => 'nullable',
												'journals.*.date' => 'required|date_format:Y-m-d',
												'journals.*.account_id' => 'required',
												'journals.*.description' => 'nullable|string',
												'journals.*.no_reference' => 'nullable|string',
												'journals.*.ledger_id' => 'required_without:journals.*.ledger_credit_id|nullable',
												'journals.*.debit' => 'required_without:journals.*.credit|nullable',
												'journals.*.credit' => 'required_without:journals.*.debit|nullable',
											],[],[
												'ledger_id' => 'Ledger',
												'date' => 'Date',
												'no_reference' => 'No Reference',
												'description' => 'Description',
												'journals' => 'Journals',
												'journals.*.id' => '',
												'journals.*.date' => 'Journal Date',
												'journals.*.account_id' => 'Journal Account',
												'journals.*.description' => 'Journal Description',
												'journals.*.no_reference' => 'Journal No Reference',
												'journals.*.ledger_id' => 'Journal Ledger',
												'journals.*.debit' => 'Journal Debit',
												'journals.*.credit' => 'Journal Credit',
											]);
		$journal->update($request->only(['ledger_id', 'date', 'no_reference', 'description']));

	foreach ($request->journals as $entryData) {
		$journal->hasmanyjournalentries()->updateOrCreate(
			['id' => $entryData['id'] ?? null],
			$entryData
		);
	}

		// foreach ($request->journals ?? [] as $journal) {
		// 	$journal->hasmanyjournalentries()->updateOrCreate(
		// 		['id' => $journal['id'] ?? null],
		// 		$journal
		// 	);
		// }

		return redirect()->route('journals.index')->with('success', 'Data Updated');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Journal $journal): JsonResponse
	{
		//
	}
}
