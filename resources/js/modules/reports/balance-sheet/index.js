const { route, url, old, errors } = window.data;

let assetsTable, liabilitiesTable, equityTable;
$('#as_of').datepicker({
	dateFormat: 'yy-mm-dd'
});

$('#as_of').on('change', generateTable);


function generateTable() {
	const asOf = $('#as_of').val();

	$.get(route.getBalanceSheetReport, { as_of: asOf }, function (res) {

		// Destroy previous tables if they exist
		if (assetsTable) assetsTable.destroy();
		if (liabilitiesTable) liabilitiesTable.destroy();
		if (equityTable) equityTable.destroy();

		$("#assets tbody, #liabilities tbody, #equity tbody").empty();

		// Assets table
		assetsTable = $('#assets').DataTable({
			paging: false,
			searching: false,
			ordering: false,
			info: false,
			data: res.assets,
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

		// Liabilities table
		liabilitiesTable = $('#liabilities').DataTable({
			paging: false,
			searching: false,
			ordering: false,
			info: false,
			data: res.liabilities,
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

		// Equity table
		equityTable = $('#equity').DataTable({
			paging: false,
			searching: false,
			ordering: false,
			info: false,
			data: res.equity,
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
		$('#totalAssets').text(Number(res.totals.totalAssets).toFixed(2));
		$('#totalLiabilities').text(Number(res.totals.totalLiabilities).toFixed(2));
		$('#totalEquity').text(Number(res.totals.totalEquity).toFixed(2));

		// Update difference/balance with color
		const balanceNum = parseFloat(res.totals.balance);
		const balanceCls = balanceNum >= 1 ? 'text-success' : 'text-danger';
		$('#balance').html(`<span class="${balanceCls}">${Number(balanceNum).toFixed(2)}</span>`);
	});
}


