//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$('#ledg').select2({
	theme: 'bootstrap-5',
	placeholder: 'Please choose',
	allowClear: true,
	closeOnSelect: true,
	width: '100%',
	ajax: {
		url: '{{ route('getLedgers') }}',
		type: 'GET',
		dataType: 'json',
		delay: 250,											// Delay to reduce server requests
		data: function (params) {
			return {
				_token: '{!! csrf_token() !!}',
				search: params.term,				// Search query
			}
		},
		processResults: function (data) {
			return {
				results: data.map(function(item) {
					return {
						id: item.id,
						text: item.belongstoaccounttype.account_type+' - '+item.ledger,
						raw: item
					}
				})
			};
		}
	},
});
@if(old('ledger_id', @$journal->ledger_id))
	$.ajax({
		url: `{{ route('getLedgers') }}`,
		data: {
			id: `{{ old('ledger_id', @$journal->ledger_id) }}`
		},
		dataType: 'json'
	}).then(data => {
		const item = Array.isArray(data) ? data[0] : data;	// change object to array
		if (!item) return;
		const option = new Option(item.belongstoaccounttype.account_type +' - '+ item.ledger, item.id, true, true);
		$('#ledg').append(option).trigger('change');
	});
@endif

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$("#date").jqueryuiDatepicker({
	dateFormat: 'yy-mm-dd',
});

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$("#journals_wrap").remAddRow({
	addBtn: "#journal_add",
	maxFields: 50,
	removeSelector: ".journal_remove",
	fieldName: "journals",
	rowIdPrefix: "journal",
	rowTemplate: (i, name) => `
		<tr id="journal_${i}">
			<td class="form-group @error('journals.*.date') has-error @enderror">
				<input type="hidden" name="${name}[${i}][id]" value="">
				<input type="text" name="${name}[${i}][date]" value="" id="date_${i}" class="form-control form-control-sm @error('journals.*.date') is-invalid @enderror" placeholder="Date">
				@error('journals.*.date')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</td>
			<td class="form-group @error('journals.*.account_id') has-error @enderror">
				<select name="${name}[${i}][account_id]" id="account_id_${i}" class="form-select form-select-sm @error('journals.*.account_id') is-invalid @enderror"></select>
				@error('journals.*.account_id')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</td>
			<!-- <td class="form-group @error('journals.*.description_debit') has-error @enderror">
				<input type="text" name="${name}[${i}][description_debit]" value="" id="description_debit_${i}" class="form-control form-control-sm @error('journals.*.description_debit') is-invalid @enderror" placeholder="Description Debit">
				@error('journals.*.description_debit')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</td> -->
			<td class="form-group @error('journals.*.no_reference_debit') has-error @enderror">
				<input type="text" name="${name}[${i}][no_reference_debit]" value="" id="no_reference_debit_${i}" class="form-control form-control-sm @error('journals.*.no_reference_debit') is-invalid @enderror" placeholder="No Reference Credit">
				@error('journals.*.no_reference_debit')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</td>
			<td class="form-group @error('journals.*.ledger_debit_id') has-error @enderror">
				<select name="${name}[${i}][ledger_debit_id]" id="ledger_debit_id_${i}" class="form-select form-select-sm @error('journals.*.ledger_debit_id') is-invalid @enderror"></select>
				@error('journals.*.ledger_debit_id')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</td>
			<td class="form-group @error('journals.*.debit') has-error @enderror">
				<input type="number" step="0.01" name="${name}[${i}][debit]" value="" id="debit_${i}" class="form-control form-control-sm @error('journals.*.debit') is-invalid @enderror" placeholder="Debit">
				@error('journals.*.debit')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</td>
			<td class="form-group @error('journals.*.credit') has-error @enderror">
				<input type="number" step="0.01" name="${name}[${i}][credit]" value="" id="credit_${i}" class="form-control form-control-sm @error('journals.*.credit') is-invalid @enderror" placeholder="Credit">
				@error('journals.*.credit')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</td>
			<td class="form-group @error('journals.*.ledger_credit_id') has-error @enderror">
				<select name="${name}[${i}][ledger_credit_id]" id="ledger_credit_id_${i}" class="form-select form-select-sm @error('journals.*.ledger_credit_id') is-invalid @enderror"></select>
				@error('journals.*.ledger_credit_id')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</td>
			<td class="form-group @error('journals.*.no_reference_credit') has-error @enderror">
				<input type="text" name="${name}[${i}][no_reference_credit]" value="" id="no_reference_credit_${i}" class="form-control form-control-sm @error('journals.*.no_reference_credit') is-invalid @enderror" placeholder="No Reference Credit">
				@error('journals.*.no_reference_credit')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</td>
			<!-- <td class="form-group @error('journals.*.description_credit') has-error @enderror">
				<input type="text" name="${name}[${i}][description_credit]" value="" id="description_credit_${i}" class="form-control form-control-sm @error('journals.*.description_credit') is-invalid @enderror" placeholder="Description Credit">
				@error('journals.*.description_credit')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</td> -->
			<td>
				<button type="button" class="btn btn-sm btn-outline-danger journal_remove" data-id="${i}"><i class="fa fa-trash"></i></button>
			</td>
		</tr>
	`,
	onAdd: (i, row) => {
		console.log("Journal added:", `journal_${i}`, row);

		$(`#account_id_${i}`).select2({
			theme: 'bootstrap-5',
			placeholder: 'Please choose',
			allowClear: true,
			closeOnSelect: true,
			width: '100%',
			ajax: {
				url: '{{ route('getAccounts') }}',
				type: 'GET',
				dataType: 'json',
				delay: 250,											// Delay to reduce server requests
				data: function (params) {
					return {
						_token: '{!! csrf_token() !!}',
						search: params.term,				// Search query
					}
				},
				processResults: function (data) {
					return {
						results: data.map(function(item) {
							return {
								id: item.id,
								text: item.code+' - '+item.account,
								raw: item
							}
						})
					};
				}
			},
		});

		$(`#date_${i}`).jqueryuiDatepicker({
			dateFormat: 'yy-mm-dd',
		});

		$(`#ledger_debit_id_${i}, #ledger_credit_id_${i}`).select2({
			theme: 'bootstrap-5',
			placeholder: 'Please choose',
			allowClear: true,
			closeOnSelect: true,
			width: '100%',
			ajax: {
				url: '{{ route('getLedgers') }}',
				type: 'GET',
				dataType: 'json',
				delay: 250,											// Delay to reduce server requests
				data: function (params) {
					return {
						_token: '{!! csrf_token() !!}',
						search: params.term,				// Search query
					}
				},
				processResults: function (data) {
					return {
						results: data.map(function(item) {
							return {
								id: item.id,
								text: item.belongstoaccounttype.account_type+' - '+item.ledger,
								raw: item
							}
						})
					};
				}
			},
		});




	},
	onRemove: (i) => {
		console.log("Journal removed:", `journal_${i}`);
	},
});

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// restore ild value
@php
	$items = @$journal?->hasmanyjournalentries()?->get(['date', 'account_id', 'description_debit', 'no_reference_debit', 'ledger_debit_id', 'debit', 'credit', 'ledger_credit_id', 'no_reference_credit', 'description_credit']);
	$itemsArray = $items?->toArray()??[];
	$oldItemsValue = old('journals', $itemsArray);
@endphp

const oldJournals = @json($oldItemsValue);
if (oldJournals.length > 0) {
	oldJournals.forEach(function (jrnl, i) {
		$("#journal_add").trigger('click');

		const $row = $("#journals_wrap").children().eq(i);

		const $account_id = $row.find(`select[name="journals[${i}][account_id]"]`);
		const $ledger_debit_id = $row.find(`select[name="journals[${i}][ledger_debit_id]"]`);
		const $ledger_credit_id = $row.find(`select[name="journals[${i}][ledger_credit_id]"]`);

		if (jrnl.account_id) {
			$.ajax({
				url: `{{ route('getAccounts') }}`,
				data: { id: jrnl.account_id },
				dataType: 'json'
			}).then(data => {
				const itema = Array.isArray(data) ? data[0] : data;	// change object to array
				if (!itema) return;
				const option1 = new Option(itema.code +' - '+ itema.account, data.id, true, true);
				$account_id.append(option1).trigger('change');
			});
		}

		if (jrnl.ledger_debit_id) {
			$.ajax({
				url: `{{ route('getLedgers') }}`,
				data: { id: jrnl.ledger_debit_id },
				dataType: 'json'
			}).then(data => {
				const itemb = Array.isArray(data) ? data[0] : data;	// change object to array
				if (!itemb) return;
				const option2 = new Option(itemb.belongstoaccounttype.account_type +' - '+ itemb.ledger, data.id, true, true);
				$ledger_debit_id.append(option2).trigger('change');
			});
		}

		if (jrnl.ledger_credit_id) {
			$.ajax({
				url: `{{ route('getLedgers') }}`,
				data: { id: jrnl.ledger_credit_id },
				dataType: 'json'
			}).then(data => {
				const itemc = Array.isArray(data) ? data[0] : data;	// change object to array
				if (!itemc) return;
				const option3 = new Option(itemc.belongstoaccounttype.account_type +' - '+ itemc.ledger, data.id, true, true);
				$ledger_credit_id.append(option3).trigger('change');
			});
		}
		$row.find(`input[name="journals[${i}][id]"]`).val(jrnl.id || '');
		$row.find(`input[name="journals[${i}][date]"]`).val(jrnl.date || '');
		// $row.find(`input[name="journals[${i}][description_debit]"]`).val(jrnl.description_debit || '');
		$row.find(`input[name="journals[${i}][no_reference_debit]"]`).val(jrnl.no_reference_debit || '');
		$row.find(`input[name="journals[${i}][debit]"]`).val(jrnl.debit || '');
		$row.find(`input[name="journals[${i}][credit]"]`).val(jrnl.credit || '');
		$row.find(`input[name="journals[${i}][no_reference_credit]"]`).val(jrnl.no_reference_credit || '');
		// $row.find(`input[name="journals[${i}][description_credit]"]`).val(jrnl.description_credit || '');
	});
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// form validation
$('#form1').bootstrapValidator({
	fields: {
		ledger_id: {
			validators: {
				notEmpty: {
					message: 'Please choose'
				},
			}
		},
		date: {
			validators: {
				notEmpty: {
					message: 'Please insert'
				},
				date: {
					format: 'YYYY-MM-DD',
					message: 'Date is not valid'
				},
			}
		},
		no_reference: {
			validators: {
			}
		},
		description: {
			validators: {
			}
		},
	<?php
		for($i = 0; $i < 51; $i++) {
	?>
		"journals[{!! $i !!}][date]": {
			validators: {
				notEmpty: {
					message: 'Please choose'
				},
				date: {
					format: 'YYYY-MM-DD',
					message: 'Date is not valid'
				},
			}
		},
		"journals[{!! $i !!}][account_id]": {
			validators: {
				notEmpty: {
					message: 'Please choose'
				},
			}
		},
		"journals[{!! $i !!}][description_debit]": {
			validators: {
			}
		},
		"journals[{!! $i !!}][no_reference_debit]": {
			validators: {
			}
		},
		"journals[{!! $i !!}][ledger_debit_id]": {
			validators: {
			}
		},
		"journals[{!! $i !!}][debit]": {
			validators: {
			}
		},
		"journals[{!! $i !!}][credit]": {
			validators: {
			}
		},
		"journals[{!! $i !!}][ledger_credit_id]": {
			validators: {
			}
		},
		"journals[{!! $i !!}][no_reference_credit]": {
			validators: {
			}
		},
		"journals[{!! $i !!}][description_credit]": {
			validators: {
			}
		},
	<?php
		}
	?>
	}
});

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
