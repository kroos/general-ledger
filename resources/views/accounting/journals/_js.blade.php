//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$('#ledg').select2({
	theme: 'bootstrap-5',
	width: '100%',
	placeholder: 'Please choose',
	allowClear: true,
	closeOnSelect: true,
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
			<td class="form-group @error('journals.*.description') has-error @enderror">
				<input type="text" name="${name}[${i}][description]" value="" id="description_${i}" class="form-control form-control-sm @error('journals.*.description') is-invalid @enderror" placeholder="Description">
				@error('journals.*.description')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</td>
			<td class="form-group @error('journals.*.no_reference') has-error @enderror">
				<input type="text" name="${name}[${i}][no_reference]" value="" id="no_reference_${i}" class="form-control form-control-sm @error('journals.*.no_reference') is-invalid @enderror" placeholder="No Reference">
				@error('journals.*.no_reference')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</td>
			<td class="form-group @error('journals.*.ledger_id') has-error @enderror">
				<select name="${name}[${i}][ledger_id]" id="ledger_id_${i}" class="form-select form-select-sm @error('journals.*.ledger_id') is-invalid @enderror"></select>
				@error('journals.*.ledger_id')
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

		$(`#ledger_id_${i}`).select2({
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
	onRemove: (i, event, $row, name) => {
		console.log("Journal removed:", `journal_${i}`);
		event.preventDefault();
		// console.log('Personnel removed', i, event, $row)
		const idv = $row.find(`input[name="${name}[${i}][id]"]`).val();
		if (!idv) {
			$row.remove();
			return;
		}
		swal.fire({
			title: 'Delete journal?',
			text: 'This action cannot be undone.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then(result => {
			if (result.isConfirmed) {
				$.ajax({
					url: `{{ url('journals') }}/${idv}`,
					type: 'DELETE',
					data: { _token: $('meta[name="csrf-token"]').attr('content') },
					success: response => {
						swal.fire('Deleted!', response.message, 'success');
						$row.remove();  // remove only after DB deletion
					},
					error: xhr => {
						swal.fire('Error', 'Failed to delete email group member', 'error');
					}
				});
			}
		});

	},
});

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// restore ild value
@php
	$items = @$journal?->hasmanyjournalentries()?->get(['id', 'date', 'account_id', 'description', 'no_reference', 'ledger_id', 'debit', 'credit']);
	$itemsArray = $items?->toArray()??[];
	$oldItemsValue = old('journals', $itemsArray);
@endphp

const oldJournals = @json($oldItemsValue);
if (oldJournals.length > 0) {
	oldJournals.forEach(function (jrnl, i) {
		$("#journal_add").trigger('click');

		const $row = $("#journals_wrap").children().eq(i);

		const $account_id = $row.find(`select[name="journals[${i}][account_id]"]`);
		const $ledger_id = $row.find(`select[name="journals[${i}][ledger_id]"]`);

		if (jrnl.account_id) {
			$.ajax({
				url: `{{ route('getAccounts') }}`,
				data: { id: jrnl.account_id },
				dataType: 'json'
			}).then(data => {
				const itema = Array.isArray(data) ? data[0] : data;	// change object to array
				if (!itema) return;
				const option1 = new Option(itema.code +' - '+ itema.account, itema.id, true, true);
				$account_id.append(option1).trigger('change');
			});
		}

		if (jrnl.ledger_id) {
			$.ajax({
				url: `{{ route('getLedgers') }}`,
				data: { id: jrnl.ledger_id },
				dataType: 'json'
			}).then(data => {
				const itemb = Array.isArray(data) ? data[0] : data;	// change object to array
				if (!itemb) return;
				const option2 = new Option(itemb.belongstoaccounttype.account_type +' - '+ itemb.ledger, itemb.id, true, true);
				$ledger_id.append(option2).trigger('change');
			});
		}

		$row.find(`input[name="journals[${i}][id]"]`).val(jrnl.id || '');
		$row.find(`input[name="journals[${i}][date]"]`).val(moment(jrnl.date).format('YYYY-MM-DD') || '');
		$row.find(`input[name="journals[${i}][description]"]`).val(jrnl.description || '');
		$row.find(`input[name="journals[${i}][no_reference]"]`).val(jrnl.no_reference || '');
		$row.find(`input[name="journals[${i}][debit]"]`).val(jrnl.debit || '');
		$row.find(`input[name="journals[${i}][credit]"]`).val(jrnl.credit || '');
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
		"journals[{!! $i !!}][description]": {
			validators: {
			}
		},
		"journals[{!! $i !!}][no_reference]": {
			validators: {
			}
		},
		"journals[{!! $i !!}][ledger_id]": {
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
	<?php
		}
	?>
	}
});

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
