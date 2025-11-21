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
////////////////////////////////////////////////////////////////////////////////////
let assetsTable, liabilitiesTable, equityTable;

////////////////////////////////////////////////////////////////////////////////////
$('#as_of').datepicker({
	dateFormat: 'yy-mm-dd'
});

$('#as_of').on('change', generateTable);

////////////////////////////////////////////////////////////////////////////////////
function generateTable() {
	const asOf = $('#as_of').val();

	$.get('{{ route("getBalanceSheetReport") }}', { as_of: asOf }, function (res) {

		// Destroy previous tables if they exist
		if (assetsTable) assetsTable.destroy();
		if (liabilitiesTable) liabilitiesTable.destroy();
		if (equityTable) equityTable.destroy();

		$("#assets tbody, #liabilities tbody, #equity tbody").empty();

		// Assets table
		assetsTable = $('#assets').DataTable({
			paging: false,
			searching: false,
			ordering: false,
			info: false,
			data: res.assets,
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

		// Liabilities table
		liabilitiesTable = $('#liabilities').DataTable({
			paging: false,
			searching: false,
			ordering: false,
			info: false,
			data: res.liabilities,
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

		// Equity table
		equityTable = $('#equity').DataTable({
			paging: false,
			searching: false,
			ordering: false,
			info: false,
			data: res.equity,
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
		$('#totalAssets').text(Number(res.totals.totalAssets).toFixed(2));
		$('#totalLiabilities').text(Number(res.totals.totalLiabilities).toFixed(2));
		$('#totalEquity').text(Number(res.totals.totalEquity).toFixed(2));

		// Update difference/balance with color
		const balanceNum = parseFloat(res.totals.balance);
		const balanceCls = balanceNum >= 1 ? 'text-success' : 'text-danger';
		$('#balance').html(`<span class="${balanceCls}">${Number(balanceNum).toFixed(2)}</span>`);
	});
}

////////////////////////////////////////////////////////////////////////////////////
@endsection
