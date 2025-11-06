@extends('layouts.app')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between align-items-center">
		<h5 class="mb-0"><i class="fa fa-book"></i> Journals</h5>
		<a href="{{ route('journals.create') }}" class="btn btn-sm btn-success">
			<i class="fa fa-plus"></i> New Journal
		</a>
	</div>
	<div class="card-body">
		<table id="journals-table" class="table table-striped table-bordered w-100"></table>
	</div>
</div>
@endsection

@section('js')
DataTable.datetime( 'D MMM YYYY' );
DataTable.datetime( 'YYYY' );
DataTable.datetime( 'h:mm a' );
// $('#journals-table').DataTable({
// 	dom: 'Bfrtip',
// 	serverSide: true,
// 	processing: true,
// 	ajax: '{{ route("journals.index") }}',
// 	columns: [
// 		{data:'id', title:'ID'},
// 		{data:'date', title:'Date'},
// 		{data:'reference_no', title:'Reference'},
// 		{data:'ledger', title:'Ledger'},
// 		{data:'status', title:'Status'},
// 		{data:'description', title:'Description'},
// 		{data:'action', title:'Actions', orderable:false, searchable:false}
// 	]
// });

$('#journals-table').DataTable({
	// columnDefs: [{ type: 'date', 'targets': [0] }],
	order: [[ 0, 'desc' ], [1, 'desc']],
	responsive: true,
	autoWidth: true,
	fixedHeader: true,
	dom: 'Bfrtip',
	ajax: {
		type: 'GET',
		url: '{{ route('getJournals') }}',
		dataSrc: '',
		data: function(da){
			da._token = '{!! csrf_token() !!}'
		},
	},
	columns: [
		{ data: 'id', title: 'ID' },
		{
			data: 'date',
			title: 'Date',
			render: function(data) {
				return moment(data).format('D MMM YYYY');
			}
		},
		{ data: 'reference_no', title: 'Reference', defaultContent: '-' },
		{ data: 'ledger_type.name', title: 'Ledger' },
		{
			data: 'status',
			title: 'Status',
			render: function(data) {
				let badge;

				switch (data) {
					case 'posted':
						badge = 'success';
						break;
					case 'draft':
						badge = 'warning';
						break;
					case 'void':
						badge = 'danger';
						break;
					default:
						badge = 'secondary';
				}

				return `<span class="badge bg-${badge}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
			}
		},
		{ data: 'description', title: 'Description', defaultContent: '-' },
		{
			data: null,
			title: '#',
			orderable: false,
			searchable: false,
			render: function(row) {
				let buttons = `<div class="btn-group btn-group-sm" role="group">`;

				if (row.status === 'draft') {
					buttons += `
					<button type="button" class="btn btn-success btn-sm btn-post" data-id="${row.id}">
						<i class="fa fa-check"></i> Post
					</button>`;
				} else if (row.status === 'posted') {
					buttons += `
					<button type="button" class="btn btn-warning btn-sm btn-unpost" data-id="${row.id}">
						<i class="fa fa-undo"></i> Unpost
					</button>`;
				}

					buttons += `
					<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="${row.id}">
						<i class="fa fa-trash"></i>
					</button>
				</div>`;

				return buttons;
			}
		}
	],
	initComplete: function(settings, response) {
		// console.log(response); // This runs after successful loading
	}
});

const table = $('#journals-table').DataTable();

function reloadTable() {
	table.ajax.reload(null, false);
}

$(document).on('click', '.btn-post', function() {
	const id = $(this).data('id');

	swal.fire({
		title: 'Post Journal?',
		text: "Are you sure you want to post this journal?",
		icon: 'question',
		showCancelButton: true,
		confirmButtonText: 'Yes, Post it!',
		cancelButtonText: 'Cancel'
	}).then((result) => {
		if (result.isConfirmed) {
			$.post(`/journals/${id}/post`, {_token: '{{ csrf_token() }}'}, function(res) {
				swal.fire('Posted!', 'The journal has been posted.', 'success');
				reloadTable();
			}).fail(() => {
				swal.fire('Error', 'Failed to post journal.', 'error');
			});
		}
	});
});


$(document).on('click', '.btn-unpost', function() {
	const id = $(this).data('id');

	swal.fire({
		title: 'Unpost Journal?',
		text: "This will revert the posting of this journal.",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'Yes, Unpost it!',
		cancelButtonText: 'Cancel'
	}).then((result) => {
		if (result.isConfirmed) {
			$.post(`/journals/${id}/unpost`, {_token: '{{ csrf_token() }}'}, function(res) {
				swal.fire('Unposted!', 'The journal has been unposted.', 'success');
				reloadTable();
			}).fail(() => {
				swal.fire('Error', 'Failed to unpost journal.', 'error');
			});
		}
	});
});

$(document).on('click', '.btn-delete', function(e){
	const id = $(this).data('id');
	swal.fire({
		title: 'Delete Journal?',
		text: 'This will soft-delete the record.',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'Yes, delete it'
	}).then(res=>{
		if(res.isConfirmed){
			$.ajax({
				url: '{{ url("journals/destroy") }}/'+id,
				type: 'DELETE',
				data: {_token:'{{ csrf_token() }}'},
				success: ()=> location.reload()
			});
		}
	});
});
@endsection
