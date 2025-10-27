<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

// for controller output
// use Illuminate\Http\RedirectResponse;
// use Illuminate\Support\Facades\Redirect;
// use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

// models
use App\Models\YesNoOption;

// load db facade
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

// load batch and queue
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

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

class YesNoOptionController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request): JsonResponse
	{
		$yno = YesNoOption::when($request->search, function (Builder $query) use ($request) {
						$query->where('option', 'LIKE', '%' . $request->search . '%');
					})
					->get(['option', 'value']);
		return response()->json($yno);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request): JsonResponse
	{
		//
	}

	/**
	 * Display the specified resource.
	 */
	public function show(YesNoOption $yesnooption): JsonResponse
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, YesNoOption $yesnooption): JsonResponse
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(YesNoOption $yesnooption): JsonResponse
	{
		//
	}
}
