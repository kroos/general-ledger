@extends('layouts.app')

@section('content')
<div class="card border-primary">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="m-0">
      <i class="fa fa-file-invoice"></i> Sales Invoice #{{ $invoice->id }}
    </h5>
    <div>
      @if($invoice->status === 'draft')
        <button id="postBtn" data-id="{{ $invoice->id }}" class="btn btn-success btn-sm">
          <i class="fa fa-check"></i> Post Invoice
        </button>
      @else
        <span class="badge bg-success"><i class="fa fa-check-circle"></i> Posted</span>
      @endif
      <a href="{{ route('accounting.sales-invoices.index') }}" class="btn btn-secondary btn-sm">
        <i class="fa fa-arrow-left"></i> Back
      </a>
    </div>
  </div>

  <div class="card-body">
    <div class="row mb-3">
      <div class="col-sm-4">
        <strong>Date:</strong> {{ $invoice->date }}
      </div>
      <div class="col-sm-4">
        <strong>Reference:</strong> {{ $invoice->reference_no ?? '-' }}
      </div>
      <div class="col-sm-4">
        <strong>Status:</strong>
        <span class="badge bg-{{ $invoice->status == 'posted' ? 'success' : 'secondary' }}">{{ ucfirst($invoice->status) }}</span>
      </div>
    </div>

    <table class="table table-bordered table-sm">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Account</th>
          <th>Description</th>
          <th class="text-end">Qty</th>
          <th class="text-end">Unit Price</th>
          <th class="text-end">Amount</th>
        </tr>
      </thead>
      <tbody>
        @foreach($invoice->items as $i => $item)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $item->account->code ?? '-' }} - {{ $item->account->name ?? '-' }}</td>
          <td>{{ $item->description }}</td>
          <td class="text-end">{{ number_format($item->quantity, 2) }}</td>
          <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
          <td class="text-end">{{ number_format($item->amount, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr><th colspan="5" class="text-end">Subtotal</th><th class="text-end">{{ number_format($invoice->subtotal, 2) }}</th></tr>
        <tr><th colspan="5" class="text-end">Tax</th><th class="text-end">{{ number_format($invoice->tax, 2) }}</th></tr>
        <tr class="table-success"><th colspan="5" class="text-end">Total</th><th class="text-end">{{ number_format($invoice->total_amount, 2) }}</th></tr>
      </tfoot>
    </table>

    @if($invoice->journal)
    <hr>
    <h6><i class="fa fa-book"></i> Journal Entries</h6>
    <table class="table table-bordered table-sm">
      <thead class="table-light">
        <tr>
          <th>Account</th>
          <th>Description</th>
          <th class="text-end">Debit</th>
          <th class="text-end">Credit</th>
        </tr>
      </thead>
      <tbody>
        @foreach($invoice->journal->entries as $entry)
        <tr>
          <td>{{ $entry->account->code ?? '-' }} - {{ $entry->account->name ?? '-' }}</td>
          <td>{{ $entry->memo ?? '-' }}</td>
          <td class="text-end">{{ number_format($entry->debit, 2) }}</td>
          <td class="text-end">{{ number_format($entry->credit, 2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
  </div>
</div>
@endsection

@section('js')
$(function() {
  $('#postBtn').on('click', function() {
    const id = $(this).data('id');
    swal.fire({
      title: 'Post this invoice?',
      text: 'A journal entry will be created automatically.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, post it!'
    }).then(res => {
      if (!res.isConfirmed) return;
      $.post(`/accounting/sales-invoices/${id}/post`,
      {_token:'{{ csrf_token() }}'
    })
      .done(() => location.reload());
    });
  });
});
@endsection
