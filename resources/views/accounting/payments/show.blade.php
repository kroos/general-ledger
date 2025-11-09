@extends('layouts.app')

@section('content')
<div class="col-sm-12">
	<div class="card border-success">
		<div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
			<span><i class="fa fa-money-check-alt"></i> Payment Details</span>
			<a href="{{ route('accounting.payments.index') }}" class="btn btn-light btn-sm">
				<i class="fa fa-arrow-left"></i> Back
			</a>
		</div>

		<div class="card-body">
			<div class="row mb-3">
				<div class="col-sm-3"><strong>Date:</strong></div>
				<div class="col-sm-9">{{ $payment->date ? \Carbon\Carbon::parse($payment->date)->format('Y-m-d') : '-' }}</div>
			</div>
			<div class="row mb-3">
				<div class="col-sm-3"><strong>Type:</strong></div>
				<div class="col-sm-9 text-capitalize">{{ $payment->type ?? '-' }}</div>
			</div>
			<div class="row mb-3">
				<div class="col-sm-3"><strong>Account:</strong></div>
				<div class="col-sm-9">{{ $payment->account->code ?? '' }} - {{ $payment->account->name ?? '' }}</div>
			</div>
			<div class="row mb-3">
				<div class="col-sm-3"><strong>Amount:</strong></div>
				<div class="col-sm-9">{{ number_format($payment->amount, 2) }}</div>
			</div>

			@if($payment->source)
			<hr>
			<h5 class="text-success">
				Linked {{ class_basename($payment->source_type) }}
			</h5>
			<table class="table table-sm table-bordered mt-2">
				<thead class="table-light">
					<tr>
						<th>Account</th>
						<th>Description</th>
						<th class="text-end">Amount</th>
					</tr>
				</thead>
				<tbody>
					@foreach($payment->source->items ?? [] as $item)
					<tr>
						<td>{{ $item->account->code ?? '' }} - {{ $item->account->name ?? '' }}</td>
						<td>{{ $item->description ?? '-' }}</td>
						<td class="text-end">{{ number_format($item->amount, 2) }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			@endif

			@if($payment->journal)
			<hr>
			<h5 class="text-primary">Journal Entries</h5>
			<table class="table table-sm table-striped table-bordered mt-2">
				<thead class="table-light">
					<tr>
						<th>Account</th>
						<th>Description</th>
						<th class="text-end">Debit</th>
						<th class="text-end">Credit</th>
					</tr>
				</thead>
				<tbody>
					@foreach($payment->journal->entries as $entry)
					<tr>
						<td>{{ $entry->account->code ?? '' }} - {{ $entry->account->name ?? '' }}</td>
						<td>{{ $entry->memo ?? '-' }}</td>
						<td class="text-end">{{ number_format($entry->debit, 2) }}</td>
						<td class="text-end">{{ number_format($entry->credit, 2) }}</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot class="fw-bold table-light">
					<tr>
						<td colspan="2" class="text-end">Total</td>
						<td class="text-end">{{ number_format($payment->journal->entries->sum('debit'), 2) }}</td>
						<td class="text-end">{{ number_format($payment->journal->entries->sum('credit'), 2) }}</td>
					</tr>
				</tfoot>
			</table>
			@endif
		</div>

		<div class="card-footer text-end">
			<a href="{{ route('accounting.payments.edit', $payment) }}" class="btn btn-warning">
				<i class="fa fa-edit"></i> Edit
			</a>
		</div>
	</div>
</div>
@endsection
