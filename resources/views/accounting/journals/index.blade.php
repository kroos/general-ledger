@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between">
		<div class="text-dark text-secondary">
			<h6>Journals</h6>
		</div>
		<div class="text-dark text-secondary">
			<a href="{{ route('journals.create') }}" class="btn btn-sm btn-outline-primary">Create New</a>
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
		getJournals: '{{ route('getJournals') }}',
	},
	url: {
		journals: '{{ url('journals') }}',
	},
	old: {},
};

@endsection
