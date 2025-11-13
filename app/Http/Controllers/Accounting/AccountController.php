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
use App\Models\Accounting\Account;

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

class AccountController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(): View
	{
		return view('accounting.accounts.index');
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): View
	{
		return view('accounting.accounts.create');
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request): RedirectResponse
	{
		$request->validate([
			'account_type_id' => 'required|integer',
			'account' => 'required|string',
			'code' => 'required|numeric',
			'description' => 'nullable|string'
		],[
			// 'account_type_id' => '',
			// 'account' => '',
			// 'code' => '',
			// 'description' => ''
		],[
			'account_type_id' => 'Account Type',
			'account' => 'Account',
			'code' => 'Code',
			'description' => 'Description'
		]);
		Account::create($request->only(['account_type_id', 'account', 'code', 'description']));
		return redirect()->route('account.index')->with('success', 'Data save');
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Account $account): View
	{
		return view('accounting.accounts.show', ['account' => $account]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Account $account): View
	{
		return view('accounting.accounts.edit', ['account' => $account]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Account $account): RedirectResponse
	{
		$request->validate([
			'account_type_id' => 'required|integer',
			'account' => 'required|string',
			'code' => 'required|numeric',
			'description' => 'nullable|string'
		],[
			// 'account_type_id' => '',
			// 'account' => '',
			// 'code' => '',
			// 'description' => ''
		],[
			'account_type_id' => 'Account Type',
			'account' => 'Account',
			'code' => 'Code',
			'description' => 'Description'
		]);
		$account->update($request->only(['account_type_id', 'account', 'code', 'description']));
		return redirect()->route('account.index')->with('success', 'Data save');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Account $account): JsonResponse
	{
		$account->delete();
		return response()->json(['success' => 'Data delete']);
	}
}
