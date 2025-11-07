@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between align-items-center">
		<h5><i class="fa fa-file-invoice-dollar"></i> Sales Invoices</h5>
		<a href="{{ route('accounting.sales-invoices.create') }}" class="btn btn-sm btn-primary">
			<i class="fa fa-plus"></i> New Invoice
		</a>
	</div>

	<div class="card-body">
		<table id="salesInvoicesTable" class="table table-bordered table-striped table-sm w-100"></table>
	</div>
</div>
@endsection

@section('js')

$('#salesInvoicesTable').DataTable({
	order: [[ 0, 'desc' ], [1, 'desc']],
	responsive: true,
	autoWidth: true,
	fixedHeader: true,
	dom: 'Bfrtip',
	ajax: {
		type: 'GET',
		url: '{{ route('getSalesInvoices') }}',
		dataSrc: '',
		data: function(da){
			da._token = '{!! csrf_token() !!}'
		},
	},
	columns: [
		{ data: 'id', title: 'ID' },
		{ data: 'date', title: 'Date', render: data => moment(data).format('DD MMM YYYY')},
		{ data: 'reference_no', title: 'Reference', defaultContent: '-' },
		{ data: 'customer_id', title: 'Customer', defaultContent: '-' },
		{ data: 'total_amount', title: 'Total Amount', render: data => parseFloat(data).toFixed(2), className: 'text-end' },
		{
			data: 'status',
			title: 'Status',
			searchable:false,
			render: function (data) {
				return data == 'posted'
				? `<span class="badge bg-success">${data}</span>`
				: `<span class="badge bg-danger">${data}</span>`;
			}
		},
		{
			data: 'id',
			title: '#',
			orderable: false,
			searchable:false,
			render: function(id){
				return `
					<a href="{{ url('accounting/sales-invoices') }}/${id}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
					<a href="{{ url('accounting/sales-invoices') }}/${id}/edit" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
					<button class="btn btn-sm btn-danger delete-btn" data-id="${id}"><i class="fa fa-trash"></i></button>
				`
			}
		}
	],
	initComplete: function(settings, response) {
		// console.log(response); // This runs after successful loading
	}
});

	$(document).on('click', '.delete-btn', function() {
		const id = $(this).data('id');
		swal.fire({
			title: 'Delete Invoice?',
			text: 'This action cannot be undone.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then(result => {
			if (result.isConfirmed) {
				$.ajax({
					url: `{{ url('accounting/sales-invoices') }}/${id}`,
					type: 'DELETE',
					data: { _token: '{{ csrf_token() }}' },
					success: () => table.ajax.reload()
				});
			}
		});
	});


@endsection
