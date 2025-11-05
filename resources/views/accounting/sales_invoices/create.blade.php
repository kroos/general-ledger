@extends('layouts.app')

@section('content')
<div class="card">
  <div class="card-header">
    <h5><i class="fa fa-plus"></i> Create Sales Invoice</h5>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ route('accounting.sales-invoices.store') }}" id="invoiceForm">
      @csrf
      @include('accounting.sales_invoices._form', ['invoice' => null])
      <div class="text-end mt-3">
        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save & Post</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('js')
$(function () {
  // Initialize Select2
  $('.select2').select2({ theme: 'bootstrap-5' });

  // Add/Remove Rows
  $('#addRow').click(function () {
    const index = $('#itemsTable tbody tr').length;
    const row = `
    <tr>
      <td>
        <select name="items[${index}][account_id]" class="form-select form-select-sm select2" required>
          <option value="">Select Account</option>
          @foreach($accounts as $id => $name)
          <option value="{{ $id }}">{{ $name }}</option>
          @endforeach
        </select>
      </td>
      <td><input type="text" name="items[${index}][description]" class="form-control form-control-sm"></td>
      <td><input type="number" step="0.01" name="items[${index}][amount]" class="form-control form-control-sm text-end" required></td>
      <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fa fa-times"></i></button></td>
    </tr>`;
    $('#itemsTable tbody').append(row);
    $('.select2').select2({ theme: 'bootstrap-5' });
  });

  $(document).on('click', '.removeRow', function() {
    $(this).closest('tr').remove();
    calcTotal();
  });

  // Calculate totals
  $(document).on('input', '#itemsTable input[name*="[amount]"], #tax', function() {
    calcTotal();
  });

  function calcTotal() {
    let subtotal = 0;
    $('#itemsTable tbody tr').each(function () {
      subtotal += parseFloat($(this).find('input[name*="[amount]"]').val()) || 0;
    });
    const tax = parseFloat($('#tax').val()) || 0;
    $('#subtotal').val(subtotal.toFixed(2));
    $('#total').val((subtotal + tax).toFixed(2));
  }

  // Restore previous Select2 if validation fails
  $('.select2').select2({ theme: 'bootstrap-5' });
});
@endsection
