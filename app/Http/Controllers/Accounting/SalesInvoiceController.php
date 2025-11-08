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
		return view('accounting.sales_invoices.create');
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
			'items.*.account_id' => 'required|numeric|exists:accounts,id',
			'items.*.description' => 'nullable|string|max:500',
			'items.*.quantity' => 'required|numeric|min:0',
			'items.*.unit_price' => 'required|numeric|min:0',
			'items.*.amount' => 'required|numeric|min:0',
		]);

    $action = $request->input('action', 'draft'); // "draft" or "post"

    try {
    	DB::beginTransaction();

			// Save base bill first
    	$invoice = SalesInvoice::create($request->only([
    		'date','supplier_id','reference_no','subtotal','tax','total','total_amount','tax_rate_percent',
    	]));
        // Create items
    	foreach ($request->items ?? [] as $item) {
    		$invoice->items()->create($item);
    	}
    	if ($action === 'draft') {
            // Just save as draft — no posting
    		$invoice->update(['status' => 'draft']);
    		DB::commit();

    		return redirect()->route('accounting.sales-invoices.index')->with('success', 'Sales Invoice saved as draft successfully.');
    	}
			// Otherwise — attempt to post immediately
    	$journal = $journalService->recordInvoice($invoice);
    	$invoice->update([
    		'journal_id' => $journal->id,
    		'status' => 'posted',
    	]);
    	DB::commit();
    	return redirect()->route('accounting.sales-invoices.index')->with('success', 'Sales Invoice posted successfully.');
    }

    // Handle expected "unbalanced" domain errors
    catch (\DomainException $e) {
    	DB::rollBack();
    	return back()->withInput()->with('danger', $e->getMessage());
    }

    // Handle other unexpected exceptions
    catch (\Throwable $e) {
    	DB::rollBack();
    	report($e);
    	return back()->withInput()->with('danger', 'Unexpected error: ' . $e->getMessage());
    }
  }

	public function show(SalesInvoice $sales_invoice)
	{
		return view('accounting.sales_invoices.show', ['invoice' => $sales_invoice->load('items.account', 'journal.entries.account')]);
	}

	public function edit(SalesInvoice $sales_invoice)
	{
		return view('accounting.sales_invoices.edit', ['invoice' => $sales_invoice->load('items')]);
	}

	// public function update(Request $request, PurchaseBill $purchase_bill, JournalService $journalService)
	public function update(Request $request, SalesInvoice $sales_invoice, JournalService $journalService)
	{
		$request->validate([
			'date' => 'required',
			'reference_no' => 'required|string|max:255',
			'supplier_id' => 'nullable',
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

		try {
			DB::beginTransaction();

			$sales_invoice->update($request->only([
				'date', 'supplier_id', 'reference_no', 'subtotal', 'tax', 'total', 'total_amount', 'tax_rate_percent'
			]));

        // Recreate items
			$sales_invoice->items()->delete();
			foreach ($request->items ?? [] as $item) {
				$sales_invoice->items()->create($item);
			}

        // Determine action
			$action = $request->input('action', 'draft');

        // Handle posting or draft saving
			if ($action === 'post') {
            // Try to rebuild and post the journal
				$journal = $journalService->recordBill($sales_invoice);
				$sales_invoice->update([
					'journal_id' => $journal->id,
					'status' => 'posted',
				]);
				$message = 'Purchase Bill updated and posted successfully.';
			} else {
            // Draft save, don't validate balancing
				$journal = $journalService->createJournal(
					SalesInvoice::class,
					$sales_invoice->id,
					$sales_invoice->items->map(fn($i) => [
						'account_id' => $i->account_id,
						'debit' => $i->amount,
						'credit' => 0,
						'memo' => $i->description,
					])->toArray(),
					'draft'
				);
				$sales_invoice->update([
					'journal_id' => $journal->id,
					'status' => 'draft',
				]);
				$message = 'Purchase Bill updated and saved as draft.';
			}

			DB::commit();

			return redirect()->route('accounting.sales-invoices.index')->with('success', $message);
		}
		catch (\DomainException $e) {
			DB::rollBack();
			return back()->withInput()->withErrors(['msg' => $e->getMessage()]);
		}
		catch (\Throwable $e) {
			DB::rollBack();
			report($e);
			return back()->withInput()->withErrors(['msg' => 'Unexpected error: ' . $e->getMessage()]);
		}
	}

	public function destroy(SalesInvoice $sales_invoice)
	{
		$sales_invoice->delete();
		return response()->json(['success' => true]);
	}
}
