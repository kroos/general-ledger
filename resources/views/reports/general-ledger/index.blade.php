@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between align-items-center">
		<h5 class="mb-0"><i class="fa fa-book"></i> General Ledger</h5>
	</div>
	<div class="card-body">

		<div class="row mb-3">
			<div class="col-md-5">
				<label class="form-label">Account</label>
				<select id="account_id" class="form-select form-select-sm select2">
					<option value="">-- Select Account --</option>
					@foreach($accounts as $acc)
					<option value="{{ $acc->id }}">{{ $acc->code }} — {{ $acc->name }}</option>
					@endforeach
				</select>
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
	$('.select2').select2({theme:'bootstrap-5'});

	$('#btnLoad').on('click', function(){
		const accId = $('#account_id').val();
		if(!accId){
			Swal.fire('Select Account', 'Please select an account first.', 'warning');
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
