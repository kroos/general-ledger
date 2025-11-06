@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between align-items-center">
		<h5><i class="fa fa-list"></i> Chart of Accounts</h5>
		<a href="{{ route('accounts.create') }}" class="btn btn-sm btn-primary">
			<i class="fa fa-plus"></i> New Account
		</a>
	</div>
	<div class="card-body">
		<table id="accountsTable" class="table table-bordered table-striped table-sm w-100"></table>
	</div>
</div>
@endsection

@section('js')

$('#accountsTable').DataTable({
	// columnDefs: [{ type: 'date', 'targets': [0] }],
	order: [[ 0, 'desc' ], [1, 'desc']],
	responsive: true,
	autoWidth: true,
	fixedHeader: true,
	dom: 'Bfrtip',
	ajax: {
		type: 'GET',
		url: '{{ route('getAccounts') }}',
		dataSrc: '',
		data: function(da){
			da._token = '{!! csrf_token() !!}'
		},
	},
	columns: [
		{ data: 'id', title: 'ID' },
		{ data: 'code', title: 'Code' },
		{ data: 'name', title: 'Name' },
		{ data: 'type', title: 'Type' },
		{ data: 'parent_id', title: 'Parent ID', defaultContent: '-' },
		{ data: 'notes', title: 'Notes', defaultContent: '-' },
		{
				data: 'active',
				title: 'Status',
				render: function (data) {
						return data == 1
								? '<span class="badge bg-success">Active</span>'
								: '<span class="badge bg-danger">Inactive</span>';
				}
		},
		{
			data: 'id',
			title: '#',
			orderable: false,
			searchable:false,
			render: function(data){
				return `
				<div class="m-0">
					<!-- <a href="/accounts/${data}" class=""><i class="fa-regular fa-file-pdf"></i></a> -->
					<a href="/accounts/${data}/edit" class=""><i class="fa-solid fa-pen-to-square"></i></a>
					<a class="text-danger delete-btn" data-id="${data}"><i class="fa-solid fa-trash-can"></i></a>
				</div>
				`
			}
		}
	],
	buttons: [
			'copy', 'csv', 'excel', 'pdf', 'print'
	],
	initComplete: function(settings, response) {
		// console.log(response); // This runs after successful loading
	}
});

$(document).on('click', '.delete-btn', function() {
	let id = $(this).data('id');
	swal.fire({
		title: 'Delete Account?',
		text: 'This action cannot be undone.',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#d33',
		confirmButtonText: 'Yes, delete it!',
	}).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: `/accounts/${id}`,
				type: 'DELETE',
				data: { _token: '{{ csrf_token() }}' },
				success: () => table.ajax.reload()
			});
		}
	});
});

@endsection
