@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between">
		<div class="text-dark text-secondary">
			<h6>Ledger</h6>
		</div>
		<div class="text-dark text-secondary">
			<a href="{{ route('ledger.create') }}" class="btn btn-sm btn-outline-primary">Create New</a>
		</div>
	</div>
	<div class="card-body">
		body
	</div>
	<div class="card-footer">
		footer
	</div>
</div>
@endsection

@section('js')
@endsection
