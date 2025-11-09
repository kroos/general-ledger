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

class AccountController extends Controller
{
	public function index(Request $request)
	{
		return view('accounting.accounts.index');
	}

	public function create()
	{
		return view('accounting.accounts.create');
	}

	public function store(Request $request)
	{
		$request->validate([
			'code' => 'required|string|max:255|unique:accounts,code',
			'name' => 'required|string|max:255',
			'type' => 'required|string',
		]);

		try {
			Account::create($request->only(['code', 'name', 'type']));
			return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
		} catch (\Throwable $e) {
			report($e);
			return back()->withInput()->with('danger', 'Unexpected error: ' . $e->getMessage());
		}
	}

	public function edit(Account $account)
	{
		return view('accounting.accounts.edit', compact('account'));
	}

	public function update(Request $request, Account $account)
	{
		$request->validate([
			'code' => 'required|string|max:255|unique:accounts,code,' . $account->id,
			'name' => 'required|string|max:255',
			'type' => 'required|string',
		]);

		try {
			$account->update($request->only(['code', 'name', 'type']));
			return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
		} catch (\Throwable $e) {
			report($e);
			return back()->withInput()->with('danger', 'Unexpected error: ' . $e->getMessage());
		}
	}

	public function destroy(Account $account)
	{
		try {
			$account->delete();
			return response()->json(['success' => true]);
		} catch (\Throwable $e) {
			return response()->json(['success' => false, 'error' => $e->getMessage()]);
		}
	}
}
