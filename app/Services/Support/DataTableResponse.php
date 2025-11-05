<?php
namespace App\Services\Support;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class DataTableResponse
{
	/**
	 * Build a DataTables-compatible JSON response.
	 *
	 * @param  Request  $request
	 * @param  Builder  $query
	 * @param  array  $columns
	 * @param  callable  $map
	 * @return JsonResponse|null
	 */
	public static function from(Request $request, Builder $query, array $columns, callable $map): ?JsonResponse
	{
			// Only handle Ajax requests
		if (! $request->ajax()) {
			return null;
		}

			// Search filter
		$searchValue = $request->input('search.value');
		if ($searchValue) {
			$query->where(function ($q) use ($searchValue, $columns) {
				foreach ($columns as $col) {
					$q->orWhere($col, 'like', "%{$searchValue}%");
				}
			});
		}

			// Sorting
		if ($request->has('order.0.column')) {
			$colIndex = $request->input('order.0.column');
			$dir = $request->input('order.0.dir', 'asc');
			$colName = $columns[$colIndex] ?? 'id';
			$query->orderBy($colName, $dir);
		} else {
			$query->orderByDesc('id');
		}

			// Paging
		$total = $query->count();
		$start = intval($request->input('start', 0));
		$length = intval($request->input('length', 10));

		$data = $query->skip($start)->take($length)->get();

			// Transform rows
		$rows = $data->map($map);

			// Return JSON formatted as DataTables expects
		return response()->json([
			'draw' => intval($request->input('draw')),
			'recordsTotal' => $total,
			'recordsFiltered' => $total,
			'data' => $rows,
		]);
	}

}
