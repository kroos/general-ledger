const { route, url, old } = window.data;

var table = $('#at').DataTable({
	...config.datatable,
	// dom: 'Bfrtip',
	ajax: {
		type: 'GET',
		url: route.getJournals,
		dataSrc: '',
		data: function(da){
		},
	},
	columns: [
		{ data: 'id', title: 'ID' },
		{ data: 'date', title: 'Date', render: data => moment(data).format('D MMM YYYY') ,defaultContent: '-' },
		{ data: 'belongstoledger.ledger', title: 'Ledger', defaultContent: '-' },
		{ data: 'no_reference', title: 'No Reference', defaultContent: '-', orderable: false, searchable:false },
		{ data: 'description', title: 'Description', defaultContent: '-', orderable: false, searchable:false },
		{
			data: 'id',
			title: '#',
			orderable: false,
			searchable:false,
			render: function(id){
				return `
					<div class="btn-group btn-group-sm" role="group">
						<a href="{{ url('journals') }}/${id}" class="btn btn-sm btn-outline-primary"><i class="fa fa-eye"></i></a>
						<a href="${url.journals}/${id}/edit" class="btn btn-sm btn-outline-info"><i class="fa fa-edit"></i></a>
						<!-- <button type="button" class="btn btn-sm btn-outline-danger remove" data-id="${id}">
							<i class="fa fa-trash"></i>
						</button> -->
					</div>
				`
			}
		}
	],
	initComplete: function(settings, response) {
		console.log(response); // This runs after successful loading
	}
});

// $(document).on('click', '.remove', function(e){
// 	const id = $(this).data('id');
// 	swal.fire({
//	...config.swal,
// 	}).then(res=>{
// 		if(res.isConfirmed){
// 			$.ajax({
// 				url: `${url.journals}/${id}`,
// 				type: 'DELETE',
// 				data: {},
// 				success: ()=> table.ajax.reload(null, false)
// 				// false = keep current page, true = reset to first page
// 			});
// 		}
// 	});
// });
