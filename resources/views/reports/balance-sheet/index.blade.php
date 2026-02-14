@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header">
		<h5 class="mb-0"><i class="fa fa-balance-scale"></i> Balance Sheet</h5>
	</div>

	<div class="card-body">
		<div class="row mb-3">
			<div class="col-md-3">
				<label for="as_of" class="col-form-label">As of Date:</label>
				<input type="text" name="as_of" id="as_of" value="" class="form-control form-control-sm">
			</div>
		</div>

		<div class="row">
			<div class="col-md-4">
				<h6 class="fw-bold">Assets</h6>
				<table id="assets" class="table table-sm table-bordered">
					<thead>
						<tr>
							<th>Account</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>
							<td>Total Assets</td><td class="text-end fw-bold" id="totalAssets"></td>
						</tr>
					</tfoot>
				</table>
			</div>

			<div class="col-md-4">
				<h6 class="fw-bold">Equity</h6>
				<table id="equity" class="table table-sm table-bordered">
					<thead>
						<tr>
							<th>Account</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>
							<td>Total Equity</td><td class="text-end fw-bold" id="totalEquity"></td>
						</tr>
					</tfoot>
				</table>
			</div>

			<div class="col-md-4">
				<h6 class="fw-bold">Liabilities</h6>
				<table id="liabilities" class="table table-sm table-bordered">
					<thead>
						<tr>
							<th>Account</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody></tbody>
					<tfoot>
						<tr>
							<td>Total Liabilities</td><td class="text-end fw-bold" id="totalLiabilities"></td>
						</tr>
					</tfoot>
				</table>
			</div>

		</div>

		<hr>
		<h5 class="text-end">
			Difference:
			 <span id="balance"></span>
		</h5>
	</div>
</div>
@endsection

@section('js')
window.data = {
	route: {
		getBalanceSheetReport: '{{ route("getBalanceSheetReport") }}',
	},
	url: {
	},
	old: {},
		errors: @json($errors->toArray()),
};

@endsection
