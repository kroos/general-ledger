@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('ledgers.update', $ledger) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
	@csrf
	@method('PATCH')
	<div class="card">
		<div class="card-header d-flex justify-content-between">
			<div class="text-dark text-secondary">
				<h6>Edit Ledger</h6>
			</div>
		</div>
		<div class="card-body">
			@include('accounting.ledgers._form')
		</div>
		<div class="card-footer d-flex justify-content-end">
			<div class="m-1">
				<button type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
			</div>
			<div class="m-1">
				<a href="{{ route('ledgers.index') }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
			</div>
		</div>
</div>
</form>
@endsection

@section('js')
	@include('accounting.ledgers._js')
@endsection
