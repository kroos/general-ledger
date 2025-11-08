@extends('layouts.app')

@section('content')
<div class="col-sm-12">
	<form method="POST" action="{{ route('accounting.payments.store') }}" id="form" autocomplete="off">
		@csrf
		<div class="card border-success">
			<div class="card-header bg-success text-white">
				<i class="fa fa-credit-card"></i> New Payment
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
$('.select2').select2({
	placeholder: 'Please choose',
	theme:'bootstrap-5',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
});


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
@if(old('account_id'))
	$.ajax({
		url: '{{ route('getAccounts') }}',
		type: 'GET',
		data: {
			search: "{{ old('account_id')}}",
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

////////////////////////////////////////////////////////////////////////////////////////////
// restore after fail form process

const oldEntries = @json(old('entries', []));
console.log(oldEntries);

// === Restore old SKILLS ===
if (oldEntries.length > 0) {
	oldEntries.forEach(function (entry, i) {
		$("#entry_add").trigger('click'); // simulate add entry
		const $entries = $("#entries").children().eq(i);
		const $entry = $entries.find(`select[name="entries[${i}][account_id]"]`);

		if (entry.account_id) {
			// Create option element manually
			const entryOption = new Option('Loading...', entry.account_id, true, true);
			$entry.append(entryOption).trigger('change');

			// Fetch actual country name asynchronously
			$.ajax({
				url: '{{ route('getAccounts') }}',
				dataType: 'json'
			}).then(data => {
				const found = data.find(d => String(d.id) === String(entry.account_id));
				if (found) {
					const option = new Option(found.code +' '+ found.name, found.id, true, true);
					$entry.empty().append(option).trigger('change');
				}
			});
		}

		$entry.find(`input[name="entries[${i}][debit]"]`).val(entry.debit || '');
		$entry.find(`input[name="entries[${i}][credit]"]`).val(entry.credit || '');
		$entry.find(`input[name="entries[${i}][description]"]`).val(entry.description || '');
	});
}

$('#type').on('change', function() {
	const val = $(this).val();
	$('.source-select').addClass('d-none');
	if (val === 'receive') $('#invoice_select_wrap').removeClass('d-none');
	if (val === 'make') $('#bill_select_wrap').removeClass('d-none');
});

@endsection
