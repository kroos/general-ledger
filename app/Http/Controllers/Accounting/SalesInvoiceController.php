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
use App\Models\Accounting\SalesInvoice;
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

class SalesInvoiceController extends Controller
{
	public function index(Request $request)
	{
		return view('accounting.sales_invoices.index');
	}

	public function create()
	{
		return view('accounting.sales_invoices.create', compact('accounts'));
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
			$invoice = SalesInvoice::create($request->only([
				'date', 'customer_id', 'reference_no', 'subtotal', 'tax', 'total_amount', 'tax_rate_percent'
			]));

			foreach ($request->items ?? [] as $item) {
				$invoice->items()->create($item);
			}
			// Create and post journal automatically
			$journal = $journalService->recordInvoice($invoice);
			$invoice->update(['journal_id' => $journal->id]);

			return redirect()->route('accounting.sales-invoices.index')->with('success', 'Invoice posted successfully.');
		});
	}

	public function show(SalesInvoice $sales_invoice)
	{
		return view('accounting.sales_invoices.show', [
			'invoice' => $sales_invoice->load('items.account', 'journal.entries.account')
		]);
	}

	public function edit(SalesInvoice $sales_invoice)
	{
		return view('accounting.sales_invoices.edit', ['invoice' => $sales_invoice->load('items')]);
	}

	public function update(Request $request, SalesInvoice $sales_invoice, JournalService $journalService)
	{
		$request->validate([
			'date' => 'required',
			'reference_no' => 'required|string|max:255',
			'customer_id' => 'nullable',
			'tax' => 'required|numeric',
			'subtotal' => 'required|numeric',
			'tax_rate_percent' => 'required|numeric',
			'total_amount' => 'required|numeric',
			'items' => 'required|array|min:1',
			'items.*.account_id' => 'required|numeric',
			'items.*.description' => 'nullable|string|max:500',
			'items.*.quantity' => 'required|numeric',
			'items.*.unit_price' => 'required|numeric',
			'items.*.amount' => 'required|numeric',
		]);

		return DB::transaction(function () use ($request, $sales_invoice, $journalService) {
			$sales_invoice->update($request->only([
				'date','customer_id','reference_no','subtotal','tax','total','total_amount', 'tax_rate_percent'
			]));

			$sales_invoice->items()->delete();
			foreach ($request->items ?? [] as $item) {
				$sales_invoice->items()->create($item);
			}

			if ($sales_invoice->journal) {
				$journalService->rebuildJournal($sales_invoice->journal, $sales_invoice->journal->entries->toArray());
			}

			return redirect()->route('accounting.sales-invoices.index')->with('success', 'Invoice updated and journal rebuilt.');
		});
	}

	public function destroy(SalesInvoice $sales_invoice)
	{
		$sales_invoice->delete();
		return response()->json(['success' => true]);
	}
}
