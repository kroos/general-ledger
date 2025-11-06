@extends('layouts.app')

@section('content')
<div class="card col-md-8 mx-auto">
	<div class="card-header"><h5><i class="fa fa-plus"></i> Edit Account</h5></div>
	<div class="card-body">
		<form method="POST" action="{{ route('accounts.update', $account) }}">
			@csrf
			@method('PATCH')
			@include('accounting.accounts._form')
		</form>
	</div>
</div>
@endsection

@section('js')
$('#type').select2({
	placeholder: 'Please choose',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
});

$('#pid').select2({
	placeholder: 'Select Account',
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
	}
});
const option = new Option('{{ $account->code.' '.$account->name }}', '{{ $account->parent_id }}', true, true);
$('#pid').empty().append(option).trigger('change');

@endsection
