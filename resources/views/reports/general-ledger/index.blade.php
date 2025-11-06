@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between align-items-center">
		<h5 class="mb-0"><i class="fa fa-book"></i> General Ledger</h5>
	</div>
	<div class="card-body">

		<div class="row mb-3">
			<div class="col-md-5 @error('account_id') has-error @enderror">
				<label for="account_id" class="form-label">Account</label>
				<select name="account_id" id="account_id" class="form-select form-select-sm select2 @error('account_id') is-invalid @enderror"></select>
			</div>
			<div class="col-md-2 my-auto">
				<button id="btnLoad" class="btn btn-primary btn-sm mt-4">
					<i class="fa fa-search"></i> Load
				</button>
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
					text: item.code + ' ' + item.name
				}))
			};
		}
	},
});

$('#btnLoad').on('click', function(){
	const accId = $('#account_id').val();
	if(!accId){
		swal.fire('Select Account', 'Please select an account first.', 'warning');
		return;
	}

	const table = $('#ledger-table').DataTable({
		destroy: true,
		ajax: {
			url: '{{ route("reports.general-ledger.index") }}',
			data: { account_id: accId }
		},
		columns: [
		{data:'date', title:'Date'},
		{data:'journal_id', title:'Journal #'},
		{data:'desc', title:'Description'},
		{data:'debit', title:'Debit', className:'text-end'},
		{data:'credit', title:'Credit', className:'text-end'},
		{data:'balance', title:'Balance', className:'text-end'},
		],
		order:[[0,'asc']]
	});
});
@endsection
