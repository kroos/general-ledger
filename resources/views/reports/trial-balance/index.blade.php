@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between align-items-center">
		<h5 class="mb-0"><i class="fa fa-balance-scale"></i> Trial Balance</h5>
	</div>

	<div class="card-body">
		<duv class="row mb-3">
			<div class="col-md-3">
				<label for="from" class="col-sm-auto col-form-label">From:</label>
				<input type="text" name="from" id="from" class="form-control form-control-sm" value="{{ @$from }}" placeholder="From">
			</div>
			<div class="col-md-3">
				<label for="to" class="col-sm-auto col-form-label">To:</label>
				<input type="text" name="to" id="to" class="form-control form-control-sm" value="{{ @$to }}" placeholder="To">
			</div>
		</duv>

		<table id="at" class="table table-sm table-striped table-bordered">
			<thead class="table-light">
				<tr>
					<th>Account</th>
					<th class="text-end">Debit</th>
					<th class="text-end">Credit</th>
					<th class="text-end">Balance</th>
					<th class="text-center">Type</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot class="fw-bold">
				<tr>
					<td class="text-end">Total</td>
					<td class="text-end" id="totalDebit">{{ number_format(@$totalDebit, 2) }}</td>
					<td class="text-end" id="totalCredit">{{ number_format(@$totalCredit, 2) }}</td>
					<td colspan="2"></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
@endsection
@section('js')
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
function generateTable() {
	if (table) {
		table.destroy();
		$("#at tbody").empty(); // clear previous rows from DOM
	}

	table = $('#at').DataTable({
		order: [[0, 'asc']],
		responsive: true,
		autoWidth: true,
		fixedHeader: true,
		dom: 'Bfrtip',
		paging: false,
		searching: false,
		ordering: false,
		info: false,
		ajax: {
			type: 'GET',
			url: '{{ route('getTrialBalanceReport') }}',
			dataSrc: 'data',
			data: function (d) {
				d.from = $('#from').val();
				d.to = $('#to').val();
			}
		},
		columns: [
		{ data: 'account' },
		{ data: 'debit', className: 'text-end' },
		{ data: 'credit', className: 'text-end' },
		{ data: 'balance', className: 'text-end' },
		{ data: 'type', className: 'text-center' },
		],
		drawCallback: function () {
			$.get('{{ route('getTrialBalanceReport') }}', {
				from: $('#from').val(),
				to: $('#to').val()
			}, function (res) {
				$('#totalDebit').text(res.totals.totalDebit);
				$('#totalCredit').text(res.totals.totalCredit);
			});
		}
	});
}

////////////////////////////////////////////////////////////////////////////////////
@endsection
