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
use App\Models\Accounting\PurchaseBill;
use App\Models\Accounting\Account;

use App\Services\Accounting\JournalService;

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

class PurchaseBillController extends Controller
{
	public function index(Request $request)
	{
		if ($request->ajax()) {
			$bills = PurchaseBill::withCount('items')->latest()->get();
			return response()->json(['data' => $bills]);
		}

		return view('accounting.purchase_bills.index');
	}

	public function create()
	{
		$accounts = Account::orderBy('code')->pluck('name', 'id');
		return view('accounting.purchase_bills.create', compact('accounts'));
	}

	public function store(Request $request, JournalService $journalService)
	{
		$request->validate([
			'date' => 'required',
			'reference_no' => 'required|string|max:255',
			'customer_id' => 'nullable',
			'tax' => 'required|numeric',
			'tax_rate_percent' => 'required|numeric',
			'subtotal' => 'required|numeric',
			'total_amount' => 'required|numeric',
			'items' => 'required|array|min:1',
			'items.*.account_id' => 'required|numeric',
			'items.*.description' => 'nullable|string|max:500',
			'items.*.quantity' => 'required|numeric',
			'items.*.unit_price' => 'required|numeric',
			'items.*.amount' => 'required|numeric',
		]);

		return DB::transaction(function () use ($request, $journalService) {
			$bill = PurchaseBill::create($request->only([
				'date','vendor_id','reference_no','subtotal','tax','total','total_amount'
			]));

			foreach ($request->items ?? [] as $item) {
				$bill->items()->create($item);
			}

						// Auto-create journal
			$journal = $journalService->recordBill($bill);
			$bill->update(['journal_id' => $journal->id]);

			return redirect()->route('accounting.purchase-bills.index')
			->with('success', 'Purchase Bill posted successfully.');
		});
	}

	public function show(PurchaseBill $purchase_bill)
	{
		return view('accounting.purchase_bills.show', [
			'bill' => $purchase_bill->load('items.account', 'journal.entries.account')
		]);
	}

	public function edit(PurchaseBill $purchase_bill)
	{
		$accounts = Account::orderBy('code')->pluck('name', 'id');
		return view('accounting.purchase_bills.edit', [
			'bill' => $purchase_bill->load('items'),
			'accounts' => $accounts
		]);
	}

	public function update(Request $request, PurchaseBill $purchase_bill, JournalService $journalService)
	{
		$request->validate([
			'date' => 'required',
			'reference_no' => 'required|string|max:255',
			'customer_id' => 'nullable',
			'tax' => 'required|numeric',
			'tax_rate_percent' => 'required|numeric',
			'subtotal' => 'required|numeric',
			'total_amount' => 'required|numeric',
			'items' => 'required|array|min:1',
			'items.*.account_id' => 'required|numeric',
			'items.*.description' => 'nullable|string|max:500',
			'items.*.quantity' => 'required|numeric',
			'items.*.unit_price' => 'required|numeric',
			'items.*.amount' => 'required|numeric',
		]);

		return DB::transaction(function () use ($request, $purchase_bill, $journalService) {
			$purchase_bill->update($request->only([
				'date','vendor_id','reference_no','subtotal','tax','total','total_amount'
			]));

			$purchase_bill->items()->delete();
			foreach ($request->items ?? [] as $item) {
				$purchase_bill->items()->create($item);
			}

			if ($purchase_bill->journal) {
				$journalService->rebuildJournal($purchase_bill->journal, $purchase_bill->journal->entries->toArray());
			}

			return redirect()->route('accounting.purchase-bills.index')
			->with('success', 'Purchase Bill updated and journal rebuilt.');
		});
	}

	public function destroy(PurchaseBill $purchase_bill)
	{
		$purchase_bill->delete();
		return response()->json(['success' => true]);
	}
}
