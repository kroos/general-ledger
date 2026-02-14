const { route, url, old, errors } = window.data;

$('#account_id').select2({
	...config.select2,
	ajax: {
		url: route.getAccounts,
		type: 'GET',
		dataType: 'json',
		data: function (params) {
			return {
				search: params.term
			};
		},
		processResults: function (data) {
			return {
				results: data.map(item => ({
					id: item.id,
					text: item.code + ' - ' + item.account
				}))
			};
		}
	},
});



$('#account_id').on('change', function(){
	let $selection = $(this).find(':selected');
	const accId = $selection.val();

	if(!accId) {
		swal.fire('Select Account', 'Please select an account first.', 'warning');
		$('#ledger-table').DataTable().clear().draw(); // or destroy
		return;
	}

	// Destroy old DataTable if exists
	if ($.fn.DataTable.isDataTable('#ledger-table')) {
		$('#ledger-table').DataTable().destroy();
	}

	$('#ledger-table').DataTable({
		...config.datatable,
		columnDefs: [
			{ type: 'date', 'targets': [0] },
		],
		order: [[0, 'asc']],
		// dom: 'Bfrtip',
		ajax: {
			type: 'GET',
			url: route.getGeneralLedgerReport,
			dataSrc: '',
			data: function(da){
				da.account_id = accId;
			},
		},
		columns: [
			{data:'date', title:'Date'},
			{data:'journal_id', title:'Journal #'},
			{data:'description', title:'Description'},
			{data:'debit', title:'Debit', className:'text-end'},
			{data:'credit', title:'Credit', className:'text-end'},
			{data:'balance', title:'Balance', className:'text-end'},
		],
		initComplete: function(settings, response) {
			// console.log(response);
		}
	});

});
