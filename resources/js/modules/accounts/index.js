const { route, url, old } = window.data;
var table = $('#at').DataTable({
	...config.datatable,
	// dom: 'Bfrtip',
	ajax: {
		type: 'GET',
		url: route.getAccounts,
		dataSrc: '',
		data: function(da){
		},
	},
	columns: [
		{ data: 'id', title: 'ID' },
		{ data: 'belongstoaccounttype.account_type', title: 'Account Type' },
		{ data: 'code', title: 'Code', defaultContent: '-' },
		{ data: 'account', title: 'Account', defaultContent: '-' },
		{ data: 'Description', title: 'Description', defaultContent: '-', orderable: false, searchable:false },
		{
			data: 'id',
			title: '#',
			orderable: false,
			searchable:false,
			render: function(id){
				return `
					<div class="btn-group btn-group-sm" role="group">
						<a href="${url.accounts}/${id}/edit" class="btn btn-sm btn-outline-info"><i class="fa fa-edit"></i></a>
						<button type="button" class="btn btn-sm btn-outline-danger remove" data-id="${id}">
							<i class="fa fa-trash"></i>
						</button>
					</div>
				`
			}
		}
	],
	initComplete: function(settings, response) {
		// console.log(response);
	}
});

$(document).on('click', '.remove', function(e){
	const id = $(this).data('id');
	swal.fire({
		...config.swal
	}).then(res=>{
		if(res.isConfirmed){
			$.ajax({
				url: url.accounts+'/'+id,
				type: 'DELETE',
				data: {
				},
				success: () => table.ajax.reload(null, false)
			});
		}
	});
});
