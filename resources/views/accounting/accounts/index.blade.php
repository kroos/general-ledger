@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between">
		<div class="text-dark text-secondary">
			<h6>Account</h6>
		</div>
		<div class="text-dark text-secondary">
			<a href="{{ route('accounts.create') }}" class="btn btn-sm btn-outline-primary">Create New</a>
		</div>
	</div>
	<div class="card-body">
		<table class="table table-hover" id="at"></table>
	</div>
	<div class="card-footer">
	</div>
</div>
@endsection

@section('js')
window.data = {
	route: {
		getAccounts: `{{ route('getAccounts') }}`,
	},
	url: {
		accounts: `{{ url('accounts') }}`,
	},
	old: {},
};
@endsection
