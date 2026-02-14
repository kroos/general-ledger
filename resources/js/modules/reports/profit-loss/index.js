
const { route, url, old, errors } = window.data;
function getError(name) {
    return errors[name] ? errors[name][0] : null;
}

let incomeTable, expenseTable;

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

function generateTable(){
	$.get(route.getProfitLossReport,
		{
			from: $('#from').val(),
			to: $('#to').val(),
		},
		function(res){

			// Destroy previous tables if they exist
			if (incomeTable) incomeTable.destroy();
			if (expenseTable) expenseTable.destroy();

			// income table
			incomeTable = $('#incomeTable').DataTable({
				paging: false,
				searching: false,
				ordering: false,
				info: false,
				data: res.incomesRows,
				columns: [
				{ data: 'account' },
				{
					data: 'amount',
					className: 'text-end',
					render: function(data) {
						const num = parseFloat(data);
						const cls = num >= 1 ? 'text-success' : 'text-danger';
						return `<span class="${cls}">${Number(num).toFixed(2)}</span>`;
					}
				}
				]
			});

			// income table
			expenseTable = $('#expenseTable').DataTable({
				paging: false,
				searching: false,
				ordering: false,
				info: false,
				data: res.expensesRows,
				columns: [
				{ data: 'account' },
				{
					data: 'amount',
					className: 'text-end',
					render: function(data) {
						const num = parseFloat(data);
						const cls = num >= 1 ? 'text-success' : 'text-danger';
						return `<span class="${cls}">${Number(num).toFixed(2)}</span>`;
					}
				}
				]
			});

			// Update totals
			$('#totalIncome').text(Number(res.totalIncome).toFixed(2));
			$('#totalExpense').text(Number(res.totalExpense).toFixed(2));

		// Update netProfit with color
		const balanceNum = parseFloat(res.netProfit);
		const balanceCls = balanceNum >= 0 ? 'text-success' : 'text-danger';
		$('#netProfit').html(`<span class="${balanceCls}">${Number(balanceNum).toFixed(2)}</span>`);
	});
}

