@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('journals.store') }}" id="journalForm">
	@csrf
	@include('accounting.journals._form')
</form>
@endsection

@section('js')
$('#ledger_type_id').select2({
	theme:'bootstrap-5',
	placeholder: 'Please choose',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
	ajax: {
		url: '{{ route('getLedgerTypes') }}', // same API you showed earlier
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
					text: item.name
				}))
			};
		}
	}
});

$("#entries").remAddRow({
	addBtn: "#entry_add",
	maxFields: 20,
	removeSelector: ".entry_remove",
	fieldName: "entries",
	rowIdPrefix: "entry",
	rowTemplate: (i, name) => `
		<tr id="entry_${i}" class="@error('entries.*.account_id') has-error @enderror">
			<td>
				<select name="${name}[${i}][account_id]" id="acc_${i}" class="form-select form-select-sm account-select @error('entries.*.account_id') is-invalid @enderror"></select>
				@error('entries.*.account_id')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror

			</td>
			<td>
				<input type="number" step="0.01" name="${name}[${i}][debit]" class="form-control form-control-sm text-end @error('entries.*.debit') is-invalid @enderror" placeholder="Debit">
				@error('entries.*.debit')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror

			</td>
			<td>
				<input type="number" step="0.01" name="${name}[${i}][credit]" class="form-control form-control-sm text-end @error('entries.*.credit') is-invalid @enderror" placeholder="Credit">
				@error('entries.*.credit')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror
			</td>
			<td>
				<input type="text" name="${name}[${i}][description]" class="form-control form-control-sm @error('entries.*.description') is-invalid @enderror" placeholder="Description">
				@error('entries.*.description')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror
			</td>
			<td>
				<button type="button" class="btn btn-sm btn-danger entry_remove"><i class="fa fa-times"></i></button>
			</td>
		</tr>
	`,
	onAdd: (i, row) => {
		console.log("Account added:", `entry_${i}`, row);
		$(`#acc_${i}`).select2({
			theme:'bootstrap-5',
			placeholder: 'Please choose',
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
	},
	onRemove: (i) => {
		console.log("Account removed:", `entry_${i}`);
	},
});

////////////////////////////////////////////////////////////////////////////////////////////
// restore after fail form process

const oldEntries = @json(old('entries', []));
console.log(oldEntries);

// === Restore old SKILLS ===
if (oldEntries.length > 0) {
	oldEntries.forEach(function (entry, i) {
		$("#entry_add").trigger('click'); // simulate add entry
		const $entries = $("#entries").children().eq(i);
		const $account = $entries.find(`select[name="entries[${i}][account_id]"]`);

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
					$account.empty().append(option).trigger('change');
				}
			});
		}

		$entries.find(`input[name="entries[${i}][debit]"]`).val(entry.debit || '');
		$entries.find(`input[name="entries[${i}][credit]"]`).val(entry.credit || '');
		$entries.find(`input[name="entries[${i}][description]"]`).val(entry.description || '');
	});
}


@endsection
