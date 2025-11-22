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
////////////////////////////////////////////////////////////////////////////////////
let incomeTable, expenseTable;

////////////////////////////////////////////////////////////////////////////////////
let table = null; // must be at the top
$('#from').datepicker({
	dateFormat: 'yy-mm-dd'
}).on('change', function () {
	$('#to').datepicker('option', 'minDate', this.value);
	if ($('#to').val()) generateTable();
});

////////////////////////////////////////////////////////////////////////////////////
$('#to').datepicker({
	dateFormat: 'yy-mm-dd'
}).on('change', function () {
	$('#from').datepicker('option', 'maxDate', this.value);
	if ($('#from').val()) generateTable();
});

////////////////////////////////////////////////////////////////////////////////////
function generateTable(){
	$.get('{{ route('getProfitLossReport') }}',
		{
			_token: '{{ csrf_token() }}',
			from: $('#from').val(),
			to: $('#to').val(),
		},
		function(res){

			// Destroy previous tables if they exist
			if (incomeTable) incomeTable.destroy();
			if (expenseTable) expenseTable.destroy();

			// income table
			incomeTable = $('#incomeTable').DataTable({
				paging: false,
				searching: false,
				ordering: false,
				info: false,
				data: res.incomesRows,
				columns: [
				{ data: 'account' },
				{
					data: 'amount',
					className: 'text-end',
					render: function(data) {
						const num = parseFloat(data);
						const cls = num >= 1 ? 'text-success' : 'text-danger';
						return `<span class="${cls}">${Number(num).toFixed(2)}</span>`;
					}
				}
				]
			});

			// income table
			expenseTable = $('#expenseTable').DataTable({
				paging: false,
				searching: false,
				ordering: false,
				info: false,
				data: res.expensesRows,
				columns: [
				{ data: 'account' },
				{
					data: 'amount',
					className: 'text-end',
					render: function(data) {
						const num = parseFloat(data);
						const cls = num >= 1 ? 'text-success' : 'text-danger';
						return `<span class="${cls}">${Number(num).toFixed(2)}</span>`;
					}
				}
				]
			});

			// Update totals
			$('#totalIncome').text(Number(res.totalIncome).toFixed(2));
			$('#totalExpense').text(Number(res.totalExpense).toFixed(2));

		// Update netProfit with color
		const balanceNum = parseFloat(res.netProfit);
		const balanceCls = balanceNum >= 0 ? 'text-success' : 'text-danger';
		$('#netProfit').html(`<span class="${balanceCls}">${Number(balanceNum).toFixed(2)}</span>`);
	});
}

@endsection
