@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between">
		<div class="text-dark text-secondary">
			<h6>Account Types</h6>
		</div>
		<div class="text-dark text-secondary">
			<a href="{{ route('account-types.create') }}" class="btn btn-sm btn-outline-primary">Create New</a>
		</div>
	</div>
	<div class="card-body">
		<table class="table table-hover" id="at">
		</table>
	</div>
	<div class="card-footer">
		footer
	</div>
</div>
@endsection

@section('js')
var table = $('#at').DataTable({
	// columnDefs: [
	// 	{ type: 'date', 'targets': [4,5,6] },
	// 	// { type: 'time', 'targets': [6] },
	// ],
	order: [[0, 'asc'], [1, 'asc']],
	responsive: true,
	autoWidth: true,
	fixedHeader: true,
	dom: 'Bfrtip',
	ajax: {
		type: 'GET',
		url: '{{ route('getAccountTypes') }}',
		dataSrc: '',
		data: function(da){
			da._token = '{!! csrf_token() !!}'
		},
	},
	columns: [
		{ data: 'id', title: 'ID' },
		{ data: 'account_type', title: 'Account Type' },
		{ data: 'description', title: 'Description', defaultContent: '-', orderable: false, searchable:false },
		{
			data: 'id',
			title: '#',
			orderable: false,
			searchable:false,
			render: function(id){
				return `
					<div class="btn-group btn-group-sm" role="group">
						<!-- <a href="{{ url('account-types') }}/${id}" class="btn btn-sm btn-outline-primary"><i class="fa fa-eye"></i></a> -->
						<a href="{{ url('account-types') }}/${id}/edit" class="btn btn-sm btn-outline-info"><i class="fa fa-edit"></i></a>
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

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$(document).on('click', '.remove', function(e){
	const id = $(this).data('id');
	swal.fire({
		title: 'Delete Account Type?',
		text: 'This will delete the account type record.',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'Yes, delete it'
	}).then(res=>{
		if(res.isConfirmed){
			$.ajax({
				url: '{{ url("account-types") }}/'+id,
				type: 'DELETE',
				data: {_token:'{{ csrf_token() }}'},
				success: ()=> table.ajax.reload(null, false)
				// false = keep current page, true = reset to first page
			});
		}
	});
});

@endsection
