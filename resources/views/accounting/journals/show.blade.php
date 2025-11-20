@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between">
		<div class="text-dark text-secondary">
			<h6>Journal Entries</h6>
		</div>
		<div class="text-dark text-secondary">
			<a href="{{ route('journals.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
		</div>
	</div>
	<div class="card-body">





		<div class="form-group row m-1">
			<label for="ledg" class="col-form-label col-sm-2">Ledger : </label>
			<div class="col-auto my-auto">{{ $journal->belongstoledger->ledger }}</div>
		</div>

		<div class="form-group row m-1">
			<label for="date" class="col-form-label col-sm-2">Date : </label>
			<div class="col-auto my-auto">{{ $journal->date->format('j M Y') }}</div>
		</div>

		<div class="form-group row m-1">
			<label for="noref" class="col-form-label col-sm-2">No Reference : </label>
			<div class="col-auto my-auto">{{ $journal->no_reference }}</div>
		</div>

		<div class="form-group row m-1 @error('description') has-error @enderror">
			<label for="desc" class="col-form-label col-sm-2">Description : </label>
			<div class="col-auto my-auto">{{ $journal->description }}</div>
		</div>

		<div>
			<table class="table table-sm table-hover">
				<thead>
					<tr>
						<th>Date</th>
						<th>Account</th>
						<th>Description</th>
						<th>No Reference</th>
						<th>Ledger</th>
						<th>Debit</th>
						<th>Credit</th>
					</tr>
				</thead>
				<tbody id="journals_wrap">
					@foreach($journal->hasmanyjournalentries()->with(['belongstoaccount', 'belongstoledger'])->get() as $k1 => $v1)
						<tr>
							<td>{{ $v1->date->format('j M Y') }}</td>
							<td>{{ $v1->belongstoaccount->code.' '.$v1->belongstoaccount->account }}</td>
							<td>{{ $v1->description }}</td>
							<td>{{ $v1->no_reference }}</td>
							<td>{{ $v1->belongstoledger->ledger }}</td>
							<td>{{ $v1->debit }}</td>
							<td>{{ $v1->credit }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>


	</div>
	<div class="card-footer">

	</div>
</div>
@endsection

@section('js')
@endsection
