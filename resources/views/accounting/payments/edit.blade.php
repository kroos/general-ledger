@extends('layouts.app')

@section('content')
<div class="col-sm-12">
  <form id="paymentForm" method="POST" action="{{ route('accounting.payments.update', $payment) }}" autocomplete="off">
      @csrf
      @method('PUT')
		<div class="card shadow shadow-lg border-success">
			<div class="card-header bg-success d-flex justify-content-between align-items-center text-white">
        <div>
  				<i class="fa fa-edit"></i> Edit Payment
        </div>
        <div>
          <a href="{{ route('accounting.payments.index') }}" class="btn btn-light btn-sm">
            <i class="fa fa-arrow-left"></i> Back
          </a>
        </div>
			</div>
			@include('accounting.payments._form')
			<div class="card-footer text-end">
				<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Record Payment</button>
				<a href="{{ route('accounting.payments.index') }}" class="btn btn-secondary">Cancel</a>
			</div>
		</div>
	</form>
</div>
@endsection

@section('js')
$(document).ready(function () {
	let invoice = `
		<div id="select" class="col-sm-12 source-select @error('source_id') has-error @enderror">
			<label for="source_id" class="col-form-label">Apply to Sales Invoice</label>
			<select name="source_id" id="source_id" class="form-select form-select-sm select2 @error('source_id') is-invalid @enderror">
				<option value="">Select invoice...</option>
				@foreach($salesInvoices as $inv)
				<option value="{{ $inv->id }}" data-source_type="App\\Models\\Accounting\\SalesInvoice" {{ (old('source_id', @$payment->source_id) == $inv->id)?'selected':NULL }}>
					Invoice #{{ $inv->reference_no }} - RM {{ number_format($inv->total_amount, 2) }}
				</option>
				@endforeach
			</select>
			<input type="hidden" name="source_type" value="App\\Models\\Accounting\\SalesInvoice">
		</div>
	`;

	let payment = `
		<div id="select" class="col-sm-12 source-select @error('source_id') has-error @enderror">
			<label for="source" class="col-form-label">Apply to Purchase Bill</label>
			<select name="source_id" id="source" class="form-select form-select-sm select2 @error('source_id') is-invalid @enderror">
				<option value="">Select bill...</option>
				@foreach($purchaseBills as $bill)
				<option value="{{ $bill->id }}" data-source_type="App\\Models\\Accounting\\PurchaseBill" {{ (old('source_id', @$payment->source_id) == $bill->id)?'selected':NULL }}>
					Bill #{{ $bill->reference_no }} - RM {{ number_format($bill->total_amount, 2) }}
				</option>
				@endforeach
			</select>
			<input type="hidden" name="source_type" value="App\\Models\\Accounting\\PurchaseBill">
		</div>
	`;

	$('#type').on('change', function () {
		const val = $(this).val();
		$('#wrap').empty();

		if (val == 'receive') {
			$('#wrap').append(invoice);
		} else if (val == 'make') {
			$('#wrap').append(payment);
		}
	});

	// --- handle old('type') after validation fail ---
	const oldType = "{{ old('type', @$payment->type) }}";
	if (oldType === 'receive') {
		$('#wrap').append(invoice);
		$('#type').val('receive');
	} else if (oldType === 'make') {
		$('#wrap').append(payment);
		$('#type').val('make');
	}
	$('select[name="source_id"]').val('{{ old('source_id', $payment->source_id) }}');

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
		}
	});
	@if(old('account_id', $payment->account_id))
		$.ajax({
			url: '{{ route('getAccounts') }}',
			type: 'GET',
			data: {
				id: "{{ old('account_id')}}",
				_token: '{{ csrf_token() }}',
			},
			success: function (response) {
				const account = Array.isArray(response) ? response[0] : response;
				if (account) {
					const option = new Option(account.code + ' ' + account.name, account.id, true, true);
					$('#account_id').append(option).trigger('change');
				}
			}
		});
	@endif
});
@endsection
