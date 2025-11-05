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
    <table id="salesInvoicesTable" class="table table-bordered table-striped table-sm w-100">
      <thead class="table-light">
        <tr>
          <th>Date</th>
          <th>Reference</th>
          <th>Customer</th>
          <th>Total</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
@endsection

@section('js')
$(function () {
  const table = $('#salesInvoicesTable').DataTable({
    ajax: '{{ route('accounting.sales-invoices.index') }}',
    columns: [
      { data: 'date', render: data => moment(data).format('YYYY-MM-DD') },
      { data: 'reference_no', defaultContent: '-' },
      { data: 'customer_id', defaultContent: '-' },
      { data: 'total', render: data => parseFloat(data).toFixed(2), className: 'text-end' },
      { data: 'status', render: s => `<span class="badge bg-${s === 'posted' ? 'success' : 'secondary'}">${s}</span>` },
      {
        data: 'id',
        render: id => `
          <a href="/accounting/sales-invoices/${id}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
          <a href="/accounting/sales-invoices/${id}/edit" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
          <button class="btn btn-sm btn-danger delete-btn" data-id="${id}"><i class="fa fa-trash"></i></button>`
      }
    ],
    order: [[0, 'desc']],
    responsive: true
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
          url: `/accounting/sales-invoices/${id}`,
          type: 'DELETE',
          data: { _token: '{{ csrf_token() }}' },
          success: () => table.ajax.reload()
        });
      }
    });
  });
});

@endsection
