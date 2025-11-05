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
		if ($request->ajax()) {
			$payments = Payment::with(['account'])->latest()->get();
			return response()->json(['data' => $payments]);
		}

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
		return DB::transaction(function () use ($request, $journalService) {
			$payment = Payment::create($request->only([
				'type','date','reference_no','amount','account_id','source_type','source_id'
			]));

			$journal = $journalService->recordPayment($payment);
			$payment->update(['journal_id' => $journal->id]);

			return redirect()->route('accounting.payments.index')
			->with('success', 'Payment recorded and journal created.');
		});
	}

	public function edit(Payment $payment)
	{
		$accounts = Account::orderBy('code')->pluck('name', 'id');
		$salesInvoices = SalesInvoice::select('id', 'reference_no', 'total_amount')->get();
		$purchaseBills = PurchaseBill::select('id', 'reference_no', 'total_amount')->get();

		return view('accounting.payments.edit', compact('payment','accounts','salesInvoices','purchaseBills'));
	}

	public function update(Request $request, Payment $payment, JournalService $journalService)
	{
		return DB::transaction(function () use ($request, $payment, $journalService) {
			$payment->update($request->only([
				'type','date','reference_no','amount','account_id','source_type','source_id'
			]));

			if ($payment->journal) {
				$journalService->rebuildJournal($payment->journal, $payment->journal->entries->toArray());
			}

			return redirect()->route('accounting.payments.index')
			->with('success', 'Payment updated and journal rebuilt.');
		});
	}

	public function destroy(Payment $payment)
	{
		$payment->delete();
		return response()->json(['success' => true]);
	}
}
