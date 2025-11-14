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
use App\Models\{
	YesNoOption, ActivityLog
};
use App\Models\Accounting\{
	Account, AccountType, Ledger, Journal, JournalEntry
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

class ModelAjaxSupportController extends Controller
{
	// this 1 need chunks sooner or later
	public function getYesNoOptions(Request $request): JsonResponse
	{
		$yno = YesNoOption::when($request->search, function (Builder $query) use ($request) {
													$query->where('option', 'LIKE', '%' . $request->search . '%');
												})
												->when($request->id, function($query) use ($request){
													$query->where('id', $request->id);
												})
												->get();
		return response()->json($yno);
	}

	public function getActivityLogs(Request $request): JsonResponse
	{
		$values = ActivityLog::with('belongstouser')
											->when($request->search, function(Builder $query) use ($request){
												$query->where('model_type','LIKE','%'.$request->search.'%')
												->orWhere('ip_address','LIKE','%'.$request->search.'%');
											})
											->when($request->id, function($query) use ($request){
												$query->where('id', $request->id);
											})
											->orderBy('created_at', 'DESC')
											->get();
		return response()->json($values);
	}

	public function getAccounts(Request $request): JsonResponse
	{
		$accounts = Account::with('belongstoaccounttype')
												->when($request->search, function (Builder $query) use ($request) {
													$query->where('account', 'LIKE', '%' . $request->search . '%')
														->orWhere('code','LIKE','%'.$request->search.'%');
												})
												->when($request->id, function($query) use ($request){
													$query->where('id', $request->id);
												})
												->get();
		return response()->json($accounts);
	}

	public function getAccountTypes(Request $request): JsonResponse
	{
		$accounttypes = AccountType::with('hasmanyaccount')
																->when($request->search, function (Builder $query) use ($request) {
																	$query->where('account_type', 'LIKE', '%' . $request->search . '%');
																})
																->when($request->id, function($query) use ($request){
																	$query->where('id', $request->id);
																})
																->get();
		return response()->json($accounttypes);
	}

	public function getLedgers(Request $request): JsonResponse
	{
		$ledgers = Ledger::with(['belongstoaccounttype', 'hasmanyjournal'])
												->when($request->search, function (Builder $query) use ($request) {
													$query->where('option', 'LIKE', '%' . $request->search . '%')
														->orWhere('ip_address','LIKE','%'.$request->search.'%');
												})
												->when($request->id, function($query) use ($request){
													$query->where('id', $request->id);
												})
												->get();
		return response()->json($ledgers);
	}

	public function getJournals(Request $request): JsonResponse
	{
		$journals = Journal::with(['belongstoledger', 'hasmanyjournalentries'])
												->when($request->search, function (Builder $query) use ($request) {
													$query->where('option', 'LIKE', '%' . $request->search . '%')
														->orWhere('ip_address','LIKE','%'.$request->search.'%');
												})
												->when($request->id, function($query) use ($request){
													$query->where('id', $request->id);
												})
												->get()->toArray();
		return response()->json($journals);
	}

	public function getJournalEntries(Request $request): JsonResponse
	{
		$journalentries = JournalEntry::with(['belongstojournal', 'belongstoaccount', 'belongstoledger'])
												->when($request->search, function (Builder $query) use ($request) {
													$query->where('no_reference', 'LIKE', '%' . $request->search . '%')
														->orWhere('description','LIKE','%'.$request->search.'%');
												})
												->when($request->id, function($query) use ($request){
													$query->where('id', $request->id);
												})
												->get();
		return response()->json($journalentries);
	}



}

