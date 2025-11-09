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
			'items.*.account_id' => 'required|exists:accounts,id',
			'items.*.description' => 'nullable|string|max:500',
			'items.*.quantity' => 'required|numeric|min:0',
			'items.*.unit_price' => 'required|numeric|min:0',
			'items.*.amount' => 'required|numeric|min:0',
		]);

		$action = $request->input('action');

		try {
			DB::beginTransaction();

			$invoice = SalesInvoice::create($request->only([
				'date', 'supplier_id', 'reference_no', 'subtotal', 'tax', 'total', 'total_amount', 'tax_rate_percent',
			]));

			foreach ($request->items ?? [] as $item) {
				$invoice->items()->create($item);
			}

			if ($action === 'draft') {
				$invoice->update(['status' => 'draft']);
				return redirect()->route('accounting.sales-invoices.index')->with('success', 'Sales Invoice saved as draft successfully.');
			} else {
				$journal = $journalService->recordInvoice($invoice);
				$invoice->update(['journal_id' => $journal->id,'status' => 'posted',]);
				return redirect()->route('accounting.sales-invoices.index')->with('success', 'Sales Invoice posted successfully.');
			}
			DB::commit();

		} catch (\DomainException $e) {
			DB::rollBack();
			return back()->withInput()->with('danger', $e->getMessage());
		} catch (\Throwable $e) {
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

	public function update(Request $request, SalesInvoice $sales_invoice, JournalService $journalService)
	{
		// dd($request->all());
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

			$sales_invoice->update($request->only([
				'date', 'supplier_id', 'reference_no', 'subtotal', 'tax', 'total', 'total_amount', 'tax_rate_percent'
			]));

			$sales_invoice->items()->delete();
			foreach ($request->items ?? [] as $item) {
				$sales_invoice->items()->create($item);
			}

			if ($action === 'post') {
				$journal = $journalService->recordInvoice($sales_invoice);
				dd($journal, $sales_invoice);
				$sales_invoice->update(['journal_id' => $journal->id, 'status' => 'posted']);
				$msg = 'Sales Invoice updated and posted successfully.';
			} else {
				$msg = 'Sales Invoice updated and saved as draft.';
				$sales_invoice->update(['status' => 'draft']);
			}

			DB::commit();
			return redirect()->route('accounting.sales-invoices.index')->with('success', $msg);
		} catch (\DomainException $e) {
			DB::rollBack();
			return back()->withInput()->with('danger', $e->getMessage());
		} catch (\Throwable $e) {
			DB::rollBack();
			report($e);
			return back()->withInput()->with('danger', 'Unexpected error: ' . $e->getMessage());
		}
	}

	public function destroy(SalesInvoice $sales_invoice)
	{
		$sales_invoice->delete();
		return response()->json(['success' => true]);
	}
}
