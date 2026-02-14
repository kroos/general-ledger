const { route, url, old } = window.data;
var table = $('#at').DataTable({
	...config.datatable,
	order: [[0, 'asc'], [1, 'asc']],
	ajax: {
		type: 'GET',
		url: route.getLedgers,
		dataSrc: '',
		data: function(da){
		},
	},
	columns: [
		{ data: 'id', title: 'ID' },
		{ data: 'belongstoaccounttype.account_type', title: 'Account Type' },
		{ data: 'ledger', title: 'Ledger', defaultContent: '-' },
		{ data: 'description', title: 'Description', defaultContent: '-', orderable: false, searchable:false },
		{
			data: 'id',
			title: '#',
			orderable: false,
			searchable:false,
			render: function(id){
				return `
					<div class="btn-group btn-group-sm" role="group">
						<!-- <a href="${url.ledgers}/${id}" class="btn btn-sm btn-outline-primary"><i class="fa fa-eye"></i></a> -->
						<a href="${url.ledgers}/${id}/edit" class="btn btn-sm btn-outline-info"><i class="fa fa-edit"></i></a>
						<button type="button" class="btn btn-sm btn-outline-danger remove" data-id="${id}">
							<i class="fa fa-trash"></i>
						</button>
					</div>
				`
			}
		}
	],
	initComplete: function(settings, response) {
		console.log(response); // This runs after successful loading
	}
});

$(document).on('click', '.remove', function(e){
	const id = $(this).data('id');
	swal.fire({
		...config.swal
	}).then(res=>{
		if(res.isConfirmed){
			$.ajax({
				url: `${url.ledgers}/${id}`,
				type: 'DELETE',
				data: {},
				success: ()=> table.ajax.reload(null, false)
				// false = keep current page, true = reset to first page
			});
		}
	});
});

