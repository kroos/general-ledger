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
});
$('.account-select').select2({
	theme:'bootstrap-5',
	placeholder: 'Please choose',
	width: '100%',
	allowClear: true,
	closeOnSelect: true,
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
				<select name="${name}[${i}][account_id]" id="acc_${i}" class="form-select form-select-sm account-select @error('entries.*.account_id') is-invalid @enderror">
					<option value="">Please choose</option>
					@foreach($accounts as $id=>$name)
					<option value="{{ $id }}">{{ $name }}</option>
					@endforeach
				</select>
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
		});
	},
	onRemove: (i) => {
		console.log("Account removed:", `entry_${i}`);
	},
});
@endsection
