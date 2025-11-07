<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

// for controller output
use Illuminate\Http\JsonResponse;
// use Illuminate\Http\RedirectResponse;
// use Illuminate\Support\Facades\Redirect;
// use Illuminate\Http\Response;
// use Illuminate\View\View;

// models
use App\Models\Accounting\{
	Journal, Account, Customer, LedgerType, Payment, Purchase, PurchaseBill, Sale, SalesInvoice, Supplier, TransactionRule,

};

// load db facade
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

// load validation
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
// use {{ namespacedRequests }}

// load batch and queue
// use Illuminate\Bus\Batch;
// use Illuminate\Support\Facades\Bus;

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

class JavaScriptSupportController extends Controller
{
	public function getAccounts(Request $request): JsonResponse
	{
		$values = Account::where('active', '1')
											->when($request->search, function($query) use ($request){
												$query->where('code','LIKE','%'.$request->search.'%')
												->orWhere('name','LIKE','%'.$request->search.'%');
											})
											->when($request->id, function($query) use ($request){
												$query->where('id', $request->id);
											})
											->orderBy('id')
											->get();
		return response()->json($values);
	}

	public function getJournals(Request $request): JsonResponse
	{
		$values = Journal::with(['ledgerType', 'entries'])
											->when($request->search, function($query) use ($request){
												$query->where('ledger_type.name','LIKE','%'.$request->search.'%')
												->orWhere('name','LIKE','%'.$request->search.'%');
											})
											->when($request->id, function($query) use ($request){
												$query->where('id', $request->id);
											})
											->orderBy('id')
											->get();
		return response()->json($values);
	}

	public function getLedgerTypes(Request $request): JsonResponse
	{
		$values = LedgerType::when($request->search, function($query) use ($request){
												$query->where('name','LIKE','%'.$request->search.'%')
												->orWhere('slug','LIKE','%'.$request->search.'%');
											})
											->when($request->id, function($query) use ($request){
												$query->where('id', $request->id);
											})
											->orderBy('id')
											->get();
		return response()->json($values);
	}

	public function getSalesInvoices(Request $request): JsonResponse
	{
		$values = SalesInvoice::with('items')
											->when($request->search, function($query) use ($request){
												$query->where('reference_no','LIKE','%'.$request->search.'%')
												->orWhere('status','LIKE','%'.$request->search.'%');
											})
											->when($request->id, function($query) use ($request){
												$query->where('id', $request->id);
											})
											->orderBy('id')
											->get();
		return response()->json($values);
	}

	public function getPurchaseBills(Request $request): JsonResponse
	{
		$values = PurchaseBill::with('items')
											->when($request->search, function($query) use ($request){
												$query->where('reference_no','LIKE','%'.$request->search.'%')
												->orWhere('status','LIKE','%'.$request->search.'%');
											})
											->when($request->id, function($query) use ($request){
												$query->where('id', $request->id);
											})
											->orderBy('id')
											->get();
		return response()->json($values);
	}

}
