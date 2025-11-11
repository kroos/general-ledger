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

	public function getYesNoOptions(Request $request): JsonResponse
	{
		$yno = YesNoOption::when($request->search, function (Builder $query) use ($request) {
						$query->where('option', 'LIKE', '%' . $request->search . '%');
					})
					->get();
		return response()->json($yno);
	}

}
