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
window.data = {
	route: {
		getAccounts: '{{ route('getAccounts') }}',
		getGeneralLedgerReport: '{{ route('getGeneralLedgerReport') }}',
	},
	url: {},
	old: {},
	errors: @json($errors->toArray())
};

@endsection
