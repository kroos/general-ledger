const { route, url, old, errors } = window.data;
function getError(name) {
    return errors[name] ? errors[name][0] : null;
}

let table = null; // must be at the top
$('#from').datepicker({
	dateFormat: 'yy-mm-dd'
}).on('change', function () {
	$('#to').datepicker('option', 'minDate', this.value);
	if ($('#to').val()) generateTable();
});

$('#to').datepicker({
	dateFormat: 'yy-mm-dd'
}).on('change', function () {
	$('#from').datepicker('option', 'maxDate', this.value);
	if ($('#from').val()) generateTable();
});

function generateTable() {
	if (table) {
		table.destroy();
		$("#at tbody").empty(); // clear previous rows from DOM
	}

	table = $('#at').DataTable({
		...config.datatable,
		order: [[0, 'asc']],
		paging: false,
		searching: false,
		ordering: false,
		info: false,
		ajax: {
			type: 'GET',
			url: route.getTrialBalanceReport,
			dataSrc: 'data',
			data: function (d) {
				d.from = $('#from').val();
				d.to = $('#to').val();
			}
		},
		columns: [
		{ data: 'account' },
		{ data: 'debit', className: 'text-end' },
		{ data: 'credit', className: 'text-end' },
		{ data: 'balance', className: 'text-end' },
		{ data: 'type', className: 'text-center' },
		],
		drawCallback: function () {
			$.get(route.getTrialBalanceReport, {
				from: $('#from').val(),
				to: $('#to').val()
			}, function (res) {
				$('#totalDebit').text(res.totals.totalDebit);
				$('#totalCredit').text(res.totals.totalCredit);
			});
		}
	});
}

