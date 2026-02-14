@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header">
		<h5 class="mb-0"><i class="fa fa-chart-line"></i> Profit & Loss Statement</h5>
	</div>
	<div class="card-body">
		<duv class="row mb-3">
			<div class="col-md-3">
				<label for="from" class="col=form-label">From:</label>
				<input type="text" name="from" id="from" value="" class="form-control form-control-sm ">
			</div>
			<div class="col-md-3">
				<label for="to" class="col=form-label">To:</label>
				<input type="text" name="to" id="to" value="" class="form-control form-control-sm ">
			</div>
		</duv>
		<div class="row">
			<div class="col-md-6">
				<h6 class="fw-bold">Income</h6>
				<table id="incomeTable" class="table table-sm table-bordered" style="width:100%">
					<thead>
						<tr>
							<th>Account Name</th>
							<th class="text-end">Amount</th>
						</tr>
					</thead>
					<tbody>
						<!-- Data will be loaded via API -->
					</tbody>
				</table>
				<div class="mt-2">
					<strong>Total Income: </strong>
					<span id="totalIncome" class="float-end">0.00</span>
				</div>
			</div>

			<div class="col-md-6">
				<h6 class="fw-bold">Expenses</h6>
				<table id="expenseTable" class="table table-sm table-bordered" style="width:100%">
					<thead>
						<tr>
							<th>Account Name</th>
							<th class="text-end">Amount</th>
						</tr>
					</thead>
					<tbody>
						<!-- Data will be loaded via API -->
					</tbody>
				</table>
				<div class="mt-2">
					<strong>Total Expenses: </strong>
					<span id="totalExpense" class="float-end">0.00</span>
				</div>
			</div>
		</div>

		<hr>
		<h5 class="text-end">
			Net Profit:
			<span id="netProfit">0.00</span>
		</h5>
	</div>
</div>
@endsection

@section('js')
window.data = {
	route: {
		getProfitLossReport: '{{ route('getProfitLossReport') }}',
	},
	url: {
	},
	old: {},
		errors: @json($errors->toArray()),
};
@endsection
