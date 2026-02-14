const { route, url, old, errors } = window.data;
function getError(name) {
    return errors[name] ? errors[name][0] : null;
}

$('#ledg').select2({
	...config.datatable,
	ajax: {
		url: route.getLedgers,
		type: 'GET',
		dataType: 'json',
		delay: 250,											// Delay to reduce server requests
		data: function (params) {
			return {
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
if(old.ledgerid){
	$.ajax({
		url: route.getLedgers,
		data: {
			id: old.ledgerid
		},
		dataType: 'json'
	}).then(data => {
		const item = Array.isArray(data) ? data[0] : data;	// change object to array
		if (!item) return;
		const option = new Option(item.belongstoaccounttype.account_type +' - '+ item.ledger, item.id, true, true);
		$('#ledg').append(option).trigger('change');
	});
}

$("#date").jqueryuiDatepicker({
	dateFormat: 'yy-mm-dd',
});

$("#journals_wrap").addRemRow({
	addBtn: '#journal_add',
	startRow: 0,
	maxRows: 50,
	fieldName: `journals`,
	rowSelector: 'journal',
	removeClass: 'journal_remove',
	// nestedwrapper: '.swrap',
	rowTemplate: (i, name) => `
		<tr id="journal_${i}" class="journal">
			<td class="form-group ${getError(`${name}.${i}.date`) ? 'has-error' : ''}">
				<input type="hidden" name="${name}[${i}][id]" value="">
				<input type="text" name="${name}[${i}][date]" value="" id="date_${i}" class="form-control form-control-sm ${getError(`${name}.${i}.date`) ? 'is-invalid' : ''}" placeholder="Date">
				${getError(`${name}.${i}.date`) ? `
					<div class="invalid-feedback">
						${getError(`${name}.${i}.date`)}
					</div>
				` : ''}
			</td>
			<td class="form-group ${getError(`${name}.${i}.account_id`) ? 'has-error' : ''}">
				<select name="${name}[${i}][account_id]" id="account_id_${i}" class="form-select form-select-sm ${getError(`${name}.${i}.account_id`) ? 'is-invalid' : ''}">
				</select>
				${getError(`${name}.${i}.account_id`) ? `
					<div class="invalid-feedback">
						${getError(`${name}.${i}.account_id`)}
					</div>
				` : ''}
			</td>
			<td class="form-group ${getError(`${name}.${i}.description`) ? `has-error` : ''}">
				<input type="text" name="${name}[${i}][description]" value="" id="description_${i}" class="form-control form-control-sm ${getError(`${name}.${i}.description`) ? `is-invalid` : ''}" placeholder="Description">
				${getError(`${name}.${i}.description`) ? `
					<div class="invalid-feedback">
						${getError(`${name}.${i}.description`)}
					</div>
			 ` : ''}
			</td>
			<td class="form-group ${getError(`${name}.${i}.no_reference`) ? `has-error` : null}">
				<input type="text" name="${name}[${i}][no_reference]" value="" id="no_reference_${i}" class="form-control form-control-sm ${getError(`${name}.${i}.no_reference`) ? `is-invalid` : ''}" placeholder="No Reference">
				${getError(`${name}.${i}.no_reference`) ? `
					<div class="invalid-feedback">
						${getError(`${name}.${i}.no_reference`)}
					</div>
				` : ''}
			</td>
			<td class="form-group ${getError(`${name}.${i}.ledger_id`) ? `has-error` : ''}">
				<select name="${name}[${i}][ledger_id]" id="ledger_id_${i}" class="form-select form-select-sm ${getError(`${name}.${i}.ledger_id`) ? `is-invalid` : ''}"></select>
				${getError(`${name}.${i}.ledger_id`) ? `
					<div class="invalid-feedback">
						${getError(`${name}.${i}.ledger_id`)}
					</div>
				` : ''}
			</td>
			<td class="form-group ${getError(`${name}.${i}.debit`) ? `has-error` : ''}">
				<input type="number" step="0.01" name="${name}[${i}][debit]" value="" id="debit_${i}" class="form-control form-control-sm ${getError(`${name}.${i}.debit`) ? `is-invalid` : ''}" placeholder="Debit">
				${getError(`${name}.${i}.debit`) ? `
					<div class="invalid-feedback">
						${getError(`${name}.${i}.debit`)}
					</div>
				` : ''}
			</td>
			<td class="form-group ${getError(`${name}.${i}.credit`) ? `has-error` : ''}">
				<input type="number" step="0.01" name="${name}[${i}][credit]" value="" id="credit_${i}" class="form-control form-control-sm ${getError(`${name}.${i}.credit`) ? `is-invalid` : ''}" placeholder="Credit">
				${getError(`${name}.${i}.credit`) ? `
					<div class="invalid-feedback">
						${getError(`${name}.${i}.credit`)}
					</div>
				` : ''}
			</td>
			<td>
				<button type="button" class="btn btn-sm btn-outline-danger journal_remove" data-id="${i}">
					<i class="fa fa-trash"></i>
				</button>
			</td>
		</tr>
	`,
	onAdd: (i, event, row, name) => {
		console.log("Journal added:", `journal_${i}`, row);

		$(`#account_id_${i}`).select2({
			...config.select2,
			ajax: {
				url: route.getAccounts,
				type: 'GET',
				dataType: 'json',
				delay: 250,											// Delay to reduce server requests
				data: function (params) {
					return {
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
			...config.select2,
			ajax: {
				url: route.getLedgers,
				type: 'GET',
				dataType: 'json',
				delay: 250,											// Delay to reduce server requests
				data: function (params) {
					return {
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
	onRemove: async (i, event, $row, name) => {
		console.log("Journal removed:", `journal_${i}`);

		const idv = $row.find(`[name="${name}[${i}][id]"]`).val();
		if (!idv) {
			return true;
		}
		swal.fire({
			...config.swal
		}).then(result => {
			if (result.isConfirmed) {
				$.ajax({
					url: `${url.journals}/${idv}`,
					type: 'DELETE',
					data: {},
					success: response => {
						swal.fire('Deleted!', response.message, 'success');
						return true;
					},
					error: xhr => {
						swal.fire('Error', 'Failed to delete email group member', 'error');
						return false;
					}
				});
			}
		});

	},
});


// restore old value
const oldJournals = old.oldJournals;
if (oldJournals.length > 0) {
	oldJournals.forEach(function (jrnl, i) {
		$("#journal_add").trigger('click');

		const $row = $("#journals_wrap").children().eq(i);

		const $account_id = $row.find(`select[name="journals[${i}][account_id]"]`);
		const $ledger_id = $row.find(`select[name="journals[${i}][ledger_id]"]`);

		if (jrnl.account_id) {
			$.ajax({
				url: route.getAccounts,
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
				url: route.getLedgers,
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
