<?php
namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;

// for controller output
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

// models
use App\Models\Accounting\Ledger;

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

class LedgerController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(): View
	{
		return view('accounting.ledgers.index');
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): View
	{
		return view('accounting.ledgers.create');
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request): RedirectResponse
	{
		$request->validate([
			'account_type_id' => 'required|integer',
			'ledger' => 'required|string',
			'description' => 'nullable|string'
		],[
			// 'account_type_id' => '',
			// 'ledger' => '',
			// 'description' => ''
		],[
			'account_type_id' => 'Account Type',
			'ledger' => 'Ledger',
			'description' => 'Description'
		]);
		Ledger::create($request->only(['account_type_id', 'ledger', 'code', 'description']));
		return redirect()->route('ledger.index')->with('success', 'Data save');
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Ledger $ledger): View
	{
		return view('accounting.ledgers.show', ['ledger' => $ledger]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Ledger $ledger): View
	{
		return view('accounting.ledgers.edit', ['ledger' => $ledger]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Ledger $ledger): RedirectResponse
	{
		$request->validate([
			'account_type_id' => 'required|integer',
			'ledger' => 'required|string',
			'description' => 'nullable|string'
		],[
			// 'account_type_id' => '',
			// 'ledger' => '',
			// 'description' => ''
		],[
			'account_type_id' => 'Account Type',
			'ledger' => 'Ledger',
			'description' => 'Description'
		]);
		$ledger->update($request->only(['account_type_id', 'ledger', 'description']));
		return redirect()->route('ledger.index')->with('success', 'Data save');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Ledger $ledger): JsonResponse
	{
		// careful with this one
		// $ledger->delete();
		return response()->json(['success' => 'Data delete']);
	}
}
