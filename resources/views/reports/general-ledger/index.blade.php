@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between align-items-center">
		<h5 class="mb-0"><i class="fa fa-book"></i> General Ledger</h5>
	</div>
	<div class="card-body">

		<div class="row mb-3">
			<div class="col-sm-12 row">
				<label for="account_id" class="col-sm-2 col-form-label">Account :</label>
				<div class="col-sm-6 my-auto">
					<select name="account_id" id="account_id" class="form-select form-select-sm select2 @error('account_id') is-invalid @enderror"></select>
				</div>
			</div>
		</div>

		<table id="ledger-table" class="table table-striped table-bordered w-100">
			<thead>
				<tr>
					<th>Date</th>
					<th>Journal #</th>
					<th>Description</th>
					<th class="text-end">Debit</th>
					<th class="text-end">Credit</th>
					<th class="text-end">Balance</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>

	</div>
</div>
@endsection

@section('js')
$('#account_id').select2({
	placeholder: 'Please choose',
	theme:'bootstrap-5',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
	ajax: {
		url: '{{ route('getAccounts') }}', // same API you showed earlier
		type: 'GET',
		dataType: 'json',
		delay: 250, // prevents excessive requests while typing
		data: function (params) {
			return {
				_token: '{{ csrf_token() }}',
				search: params.term // optional if you want filtering
			};
		},
		processResults: function (data) {
			// API returns plain array, so map it to Select2 format
			return {
				results: data.map(item => ({
					id: item.id,
					text: item.code + ' - ' + item.account
				}))
			};
		}
	},
});


});$('#account_id').on('change', function(){
	let $selection = $(this).find(':selected');
	const accId = $selection.val();

	if(!accId) {
		swal.fire('Select Account', 'Please select an account first.', 'warning');
		$('#ledger-table').DataTable().clear().draw(); // or destroy
		return;
	}

	// Destroy old DataTable if exists
	if ($.fn.DataTable.isDataTable('#ledger-table')) {
		$('#ledger-table').DataTable().destroy();
	}

	$('#ledger-table').DataTable({
		columnDefs: [
			{ type: 'date', 'targets': [0] },
		],
		order: [[0, 'asc']],
		responsive: true,
		autoWidth: true,
		fixedHeader: true,
		dom: 'Bfrtip',
		ajax: {
			type: 'GET',
			url: '{{ route('reports.general-ledger.index') }}',
			dataSrc: 'data',
			data: function(da){
				da._token = '{!! csrf_token() !!}';
				da.account_id = accId;
			},
		},
		columns: [
			{data:'date', title:'Date'},
			{data:'journal_id', title:'Journal #'},
			{data:'description', title:'Description'},
			{data:'debit', title:'Debit', className:'text-end'},
			{data:'credit', title:'Credit', className:'text-end'},
			{data:'balance', title:'Balance', className:'text-end'},
		],
		initComplete: function(settings, response) {
			console.log(response);
		}
	});
@endsection
