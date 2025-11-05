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
		if ($request->ajax()) {
			$accounts = $this->buildAccountTree();
			return response()->json(['data' => $accounts]);
		}

		return view('accounting.accounts.index');
	}

	public function create()
	{
		$parents = Account::orderBy('code')->pluck('name', 'id');
		return view('accounting.accounts.create', compact('parents'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'code' => 'required|string|max:20|unique:accounts,code',
			'name' => 'required|string|max:255',
			'type' => 'required|in:asset,liability,equity,income,expense',
			'parent_id' => 'nullable|exists:accounts,id',
			'description' => 'nullable|string|max:500',
		]);

		DB::transaction(function () use ($validated) {
			Account::create($validated);
		});

		return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
	}

	public function edit(Account $account)
	{
		$parents = Account::where('id', '!=', $account->id)->orderBy('code')->pluck('name', 'id');
		return view('accounting.accounts.edit', compact('account', 'parents'));
	}

	public function update(Request $request, Account $account)
	{
		$validated = $request->validate([
			'code' => 'required|string|max:20|unique:accounts,code,' . $account->id,
			'name' => 'required|string|max:255',
			'type' => 'required|in:asset,liability,equity,income,expense',
			'parent_id' => 'nullable|exists:accounts,id',
			'description' => 'nullable|string|max:500',
		]);

		DB::transaction(function () use ($account, $validated) {
			$account->update($validated);
		});

		return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
	}

	public function destroy(Account $account)
	{
		$account->delete();
		return response()->json(['success' => true]);
	}

	protected function buildAccountTree($parentId = null, $depth = 0)
	{
		$accounts = \App\Models\Accounting\Account::where('parent_id', $parentId)
		->orderBy('code')
		->get();

		$result = [];
		foreach ($accounts as $acc) {
			$acc->indent_level = $depth;
			$result[] = $acc;
			$result = array_merge($result, $this->buildAccountTree($acc->id, $depth + 1));
		}
		return $result;
	}


}
