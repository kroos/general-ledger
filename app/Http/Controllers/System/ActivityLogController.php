<?php
namespace App\Http\Controllers\System;
use App\Http\Controllers\Controller;

// for controller output
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Response;
use Illuminate\View\View;

// models
use App\Models\ActivityLog;

use App\Services\Support\DataTableResponse;

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

class ActivityLogController extends Controller
{
	public function index(Request $request)
	{
		$query = ActivityLog::with('user')
		->select(['id','event','model_type','model_id','user_id','changes','ip_address','user_agent','created_at']);

		if ($response = DataTableResponse::from(
			$request,
			$query,
			['event','model_type','model_id','ip_address','created_at'],
			function ($log) {
				return [
					'id' => $log->id,
					'event' => ucfirst($log->event),
					'model' => class_basename($log->model_type).' #'.$log->model_id,
					'user' => $log->user?->name ?? 'System',
					'changes' => $log->changes ? json_encode($log->changes, JSON_PRETTY_PRINT) : '-',
					'ip' => $log->ip_address,
					'created_at' => $log->created_at->format('Y-m-d H:i:s'),
					'action' => view('system.activity_logs._actions', compact('log'))->render(),
				];
			}
		)) {
			return $response;
		}

		return view('system.activity_logs.index');
	}

	public function show(ActivityLog $log)
	{
		return view('system.activity_logs.show', compact('log'));
	}

	public function destroy(ActivityLog $log)
	{
		$log->delete();
		return response()->json(['success' => true]);
	}
}
