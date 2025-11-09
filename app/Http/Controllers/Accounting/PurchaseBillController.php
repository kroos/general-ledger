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
		return view('accounting.purchase_bills.index');
	}

	public function create()
	{
		return view('accounting.purchase_bills.create');
	}

	public function store(Request $request, JournalService $journalService)
	{
		$request->validate([
			'date' => 'required|date',
			'reference_no' => 'required|string|max:255',
			'supplier_id' => 'nullable',
			'tax' => 'required|numeric',
			'tax_rate_percent' => 'required|numeric',
			'subtotal' => 'required|numeric',
			'total_amount' => 'required|numeric',
			'items' => 'required|array|min:1',
			'items.*.account_id' => 'required|exists:accounts,id',
			'items.*.description' => 'nullable|string|max:500',
			'items.*.quantity' => 'required|numeric|min:0',
			'items.*.unit_price' => 'required|numeric|min:0',
			'items.*.amount' => 'required|numeric|min:0',
		]);

		$action = $request->input('action');

		try {
			DB::beginTransaction();

			$bill = PurchaseBill::create($request->only([
				'date', 'supplier_id', 'reference_no', 'subtotal', 'tax', 'total', 'total_amount', 'tax_rate_percent',
			]));

			foreach ($request->items ?? [] as $item) {
				$bill->items()->create($item);
			}

			if ($action === 'draft') {
				$bill->update(['status' => 'draft']);
				DB::commit();
				return redirect()->route('accounting.purchase-bills.index')->with('success', 'Purchase Bill saved as draft successfully.');
			}

			$journal = $journalService->recordBill($bill);
			$bill->update(['journal_id' => $journal->id, 'status' => 'posted']);

			DB::commit();
			return redirect()->route('accounting.purchase-bills.index')->with('success', 'Purchase Bill posted successfully.');
		} catch (\DomainException $e) {
			DB::rollBack();
			return back()->withInput()->with('danger', $e->getMessage());
		} catch (\Throwable $e) {
			DB::rollBack();
			report($e);
			return back()->withInput()->with('danger', 'Unexpected error: ' . $e->getMessage());
		}
	}

	public function show(PurchaseBill $purchase_bill)
	{
		return view('accounting.purchase_bills.show', ['bill' => $purchase_bill->load('items.account', 'journal.entries.account')]);
	}

	public function edit(PurchaseBill $purchase_bill)
	{
		return view('accounting.purchase_bills.edit', ['bill' => $purchase_bill->load('items')]);
	}

	public function update(Request $request, PurchaseBill $purchase_bill, JournalService $journalService)
	{
		$request->validate([
			'date' => 'required|date',
			'reference_no' => 'required|string|max:255',
			'supplier_id' => 'nullable',
			'tax' => 'required|numeric',
			'tax_rate_percent' => 'required|numeric',
			'subtotal' => 'required|numeric',
			'total_amount' => 'required|numeric',
			'items' => 'required|array|min:1',
			'items.*.account_id' => 'required|exists:accounts,id',
			'items.*.description' => 'nullable|string|max:500',
			'items.*.quantity' => 'required|numeric|min:0',
			'items.*.unit_price' => 'required|numeric|min:0',
			'items.*.amount' => 'required|numeric|min:0',
		]);

		$action = $request->input('action');

		try {
			DB::beginTransaction();

			$purchase_bill->update($request->only([
				'date', 'supplier_id', 'reference_no', 'subtotal', 'tax', 'total', 'total_amount', 'tax_rate_percent',
			]));

			$purchase_bill->items()->delete();
			foreach ($request->items ?? [] as $item) {
				$purchase_bill->items()->create($item);
			}

			if ($action === 'draft') {
				$purchase_bill->update(['status' => 'draft']);
				DB::commit();
				return redirect()->route('accounting.purchase-bills.index')->with('success', 'Purchase Bill saved as draft successfully.');
			} else {
				// $this->safeRecord(fn() => $this->journalService->recordBill($purchase_bill), 'PurchaseBill update');
				$journalService->recordBill($purchase_bill);
				$purchase_bill->update(['journal_id' => $journal->id, 'status' => 'posted']);
				DB::commit();
				return redirect()->route('accounting.purchase-bills.index')->with('success', 'Purchase Bill posted successfully.');
			}

		} catch (\DomainException $e) {
			DB::rollBack();
			return back()->withInput()->with('danger', $e->getMessage());
		} catch (\Throwable $e) {
			DB::rollBack();
			report($e);
			return back()->withInput()->with('danger', 'Unexpected error: ' . $e->getMessage());
		}
	}

	public function destroy(PurchaseBill $purchase_bill)
	{
		$purchase_bill->delete();
		return response()->json(['success' => true]);
	}
}
