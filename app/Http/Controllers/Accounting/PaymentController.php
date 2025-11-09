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
use App\Models\Accounting\Payment;
use App\Models\Accounting\Account;
use App\Models\Accounting\SalesInvoice;
use App\Models\Accounting\PurchaseBill;

// service
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

class PaymentController extends Controller
{
	public function index(Request $request)
	{
		return view('accounting.payments.index');
	}

	public function create()
	{
		$accounts = Account::orderBy('code')->pluck('name', 'id');
		$salesInvoices = SalesInvoice::select('id', 'reference_no', 'total_amount')->get();
		$purchaseBills = PurchaseBill::select('id', 'reference_no', 'total_amount')->get();

		return view('accounting.payments.create', compact('accounts', 'salesInvoices', 'purchaseBills'));
	}

	public function store(Request $request, JournalService $journalService)
	{
		$request->validate([
			'date' => 'required|date',
			'reference_no' => 'required|string|max:255',
			'type' => 'required',
			'account_id' => 'required|exists:accounts,id',
			'amount' => 'required|numeric|min:0.01',
		]);

		try {
			DB::beginTransaction();

			$payment = Payment::create($request->only(['date', 'reference_no', 'type', 'account_id', 'amount']));

			$journal = $journalService->recordPayment($payment);

			$payment->update([
				'journal_id' => $journal->id,
				'status' => 'posted',
			]);

			DB::commit();
			return redirect()->route('accounting.payments.index')->with('success', 'Payment posted successfully.');
		} catch (\DomainException $e) {
			DB::rollBack();
			return back()->withInput()->with('danger', $e->getMessage());
		} catch (\Throwable $e) {
			DB::rollBack();
			report($e);
			return back()->withInput()->with('danger', 'Unexpected error: ' . $e->getMessage());
		}
	}

	public function show(Payment $payment)
	{
		$payment->load([
			'account',
			'journal.entries.account',
			'source' => function ($query) {
				$query->with('items.account');
			}
		]);

		return view('accounting.payments.show', compact('payment'));
	}


	public function edit(Payment $payment)
	{
		$accounts = Account::orderBy('code')->get(['id', 'code', 'name']);
		$salesInvoices = SalesInvoice::select('id', 'reference_no', 'total_amount')->get();
		$purchaseBills = PurchaseBill::select('id', 'reference_no', 'total_amount')->get();
		$sourceTypes = PurchaseBill::select('id', 'reference_no', 'total_amount')->get();

		return view('accounting.payments.edit', compact('payment','accounts','salesInvoices','purchaseBills', 'sourceTypes'));
	}

	public function update(Request $request, Payment $payment, JournalService $journalService)
	{
		$request->validate([
			'date' => 'required|date',
			'reference_no' => 'required|string|max:255',
			'type' => 'required',
			'account_id' => 'required|exists:accounts,id',
			'amount' => 'required|numeric|min:0.01',
		]);

		try {
			DB::beginTransaction();

			$payment->update($request->only(['date', 'reference_no', 'type', 'account_id', 'amount']));

			$journal = $journalService->recordPayment($payment);

			$payment->update([
				'journal_id' => $journal->id,
				'status' => 'posted',
			]);

			DB::commit();
			return redirect()->route('accounting.payments.index')->with('success', 'Payment updated and posted successfully.');
		} catch (\DomainException $e) {
			DB::rollBack();
			return back()->withInput()->with('danger', $e->getMessage());
		} catch (\Throwable $e) {
			DB::rollBack();
			report($e);
			return back()->withInput()->with('danger', 'Unexpected error: ' . $e->getMessage());
		}
	}

	public function destroy(Payment $payment)
	{
		try {
			$payment->delete();
			return response()->json(['success' => true]);
		} catch (\Throwable $e) {
			return response()->json(['success' => false, 'error' => $e->getMessage()]);
		}
	}
}
