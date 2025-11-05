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
    <table id="accountsTable" class="table table-bordered table-striped table-sm w-100">
      <thead class="table-light">
        <tr>
          <th>Account</th>
          <th>Type</th>
          <th>Parent</th>
          <th>Description</th>
          <th>Created</th>
          <th>Action</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
@endsection

@section('js')
$(function() {
  let table = $('#accountsTable').DataTable({
    ajax: '{{ route('accounts.index') }}',
    columns: [
      {
        data: null,
        render: data => {
          let indent = '&nbsp;&nbsp;&nbsp;&nbsp;'.repeat(data.indent_level);
          let icon = data.parent_id ? '<i class="fa fa-level-up fa-rotate-90 text-muted"></i> ' : '';
          return indent + icon + `${data.code} - ${data.name}`;
        }
      },
      { data: 'type', className: 'text-capitalize' },
      { data: 'parent.name', defaultContent: '-' },
      { data: 'description', defaultContent: '-' },
      { data: 'created_at', render: data => moment(data).format('YYYY-MM-DD') },
      {
        data: 'id',
        render: id => `
        <a href="/accounts/${id}/edit" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
        <button class="btn btn-sm btn-danger delete-btn" data-id="${id}"><i class="fa fa-trash"></i></button>
        `
      }
    ],
    order: [],
  });

  $(document).on('click', '.delete-btn', function() {
    let id = $(this).data('id');
    Swal.fire({
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
});
@endsection
