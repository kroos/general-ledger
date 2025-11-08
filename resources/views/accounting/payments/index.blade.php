@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-primary">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class="fa fa-credit-card"></i> Payments</h5>
    <a href="{{ route('accounting.payments.create') }}" class="btn btn-light btn-sm">
      <i class="fa fa-plus"></i> New Payment
    </a>
  </div>

  <div class="card-body">
    <table id="paymentsTable" class="table table-bordered table-striped table-sm w-100"></table>
  </div>
</div>
@endsection

@section('js')

//  const table = $('#paymentsTable').DataTable({
//    ajax: '{{ route('accounting.payments.index') }}',
//    columns: [
//      { data: 'date', render: data => moment(data).format('YYYY-MM-DD') },
//      { data: 'reference_no', defaultContent: '-' },
//      {
//        data: 'type',
//        render: data => {
//          const badge = data === 'receive' ? 'success' : 'danger';
//          const label = data === 'receive' ? 'Received' : 'Paid';
//          return `<span class="badge bg-${badge}">${label}</span>`;
//        }
//      },
//      { data: 'account.name', defaultContent: '-' },
//      {
//        data: 'amount',
//        className: 'text-end',
//        render: data => `RM${parseFloat(data || 0).toFixed(2)}`
//      },
//      {
//        data: 'status',
//        render: data => {
//          const badge = data === 'posted' ? 'success' : 'warning';
//          return `<span class="badge bg-${badge}">${data}</span>`;
//        }
//      },
//      { data: 'user.name', defaultContent: '-' },
//      {
//        data: 'id',
//        orderable: false,
//        render: id => `
//          <a href="{{ url('payments') }}/${id}" class="btn btn-sm btn-info" title="View">
//            <i class="fa fa-eye"></i>
//          </a>
//          <a href="/payments/${id}/edit" class="btn btn-sm btn-warning" title="Edit">
//            <i class="fa fa-edit"></i>
//          </a>
//          <button class="btn btn-sm btn-danger delete-btn" data-id="${id}" title="Delete">
//            <i class="fa fa-trash"></i>
//          </button>
//        `
//      }
//    ],
//    order: [[0, 'desc']],
//    responsive: true,
//    autoWidth: false,
//    language: {
//      emptyTable: "No payments recorded yet."
//    }
//  });


$('#paymentsTable').DataTable({
  lengthMenu: [ [10, 50, 100, 200, -1], [10, 50, 100, 200, 'All'] ],
  order: [[ 0, 'desc' ], [1, 'desc']],
  responsive: true,
  autoWidth: true,
  // fixedHeader: true,
  // dom: 'Bfrtip',
  ajax: {
    type: 'GET',
    url: '{{ route('getPayments') }}',
    dataSrc: '',
    data: function(da){
      da._token = '{!! csrf_token() !!}'
    },
  },
  columns: [
    { data: 'id', title: 'ID' },
    { data: 'date', title: 'Date', render: data => moment(data).format('DD MMM YYYY')},
    { data: 'reference_no', title: 'Reference', defaultContent: '-' },
    {
      data: 'type',
      title: 'Type',
      render: data => {
        const badge = data === 'receive' ? 'success' : 'danger';
        const label = data === 'receive' ? 'Received' : 'Paid';
        return `<span class="badge bg-${badge}">${label}</span>`;
      }
    },
    {
      data: null,
      title: 'Account',
      defaultContent: '-',
      render: function (data, type, row) {
        // console.log(data);
        let modelName = row.account.name ?? '';
        let modelCode = row.account.code ?? '';
        return `${modelCode} ${modelName}`;
      }
    },
    {
     data: 'amount',
     title: 'Amount',
     className: 'text-end',
     render: data => `RM${parseFloat(data || 0).toFixed(2)}`
    },
    {
      data: 'status',
      title: 'Status',
      render: data => {
        const badge = data === 'posted' ? 'success' : 'warning';
        return `<span class="badge bg-${badge}">${data}</span>`;
      }
    },
    {
      data: 'id',
      title: '#',
      orderable: false,
      render: id => `
        <a href="{{ url('payments') }}/${id}" class="btn btn-sm btn-info" title="View">
          <i class="fa fa-eye"></i>
        </a>
        <a href="/payments/${id}/edit" class="btn btn-sm btn-warning" title="Edit">
          <i class="fa fa-edit"></i>
        </a>
        <button class="btn btn-sm btn-danger delete-btn" data-id="${id}" title="Delete">
          <i class="fa fa-trash"></i>
        </button>
      `
    }
  ],
  initComplete: function(settings, response) {
    // console.log(response); // This runs after successful loading
  }
});























  // SweetAlert2 delete confirmation
  $(document).on('click', '.delete-btn', function() {
    const id = $(this).data('id');
    swal.fire({
      title: 'Delete Payment?',
      text: 'This will permanently delete the record and its journal entry.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: '<i class="fa fa-trash"></i> Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: `/accounting/payments/${id}`,
          type: 'DELETE',
          data: { _token: '{{ csrf_token() }}' },
          success: () => {
            table.ajax.reload();
            swal.fire({
              icon: 'success',
              title: 'Deleted!',
              text: 'The payment has been removed.',
              timer: 1500,
              showConfirmButton: false
            });
          },
          error: () => {
            swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Failed to delete payment. Please try again later.'
            });
          }
        });
      }
    });
  });





@endsection
