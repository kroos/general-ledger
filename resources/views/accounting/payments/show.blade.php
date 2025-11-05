@extends('layouts.app')

@section('content')
<div class="card border-primary shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
    <h5 class="mb-0"><i class="fa fa-credit-card"></i> Payment Details</h5>
    <div>
      <a href="{{ route('accounting.payments.index') }}" class="btn btn-sm btn-light">
        <i class="fa fa-arrow-left"></i> Back
      </a>
    </div>
  </div>

  <div class="card-body">
    <div class="row mb-3">
      <div class="col-sm-4"><strong>Type:</strong> {{ ucfirst($payment->type) }}</div>
      <div class="col-sm-4"><strong>Date:</strong> {{ $payment->date->format('Y-m-d') }}</div>
      <div class="col-sm-4"><strong>Status:</strong>
        <span class="badge bg-{{ $payment->status === 'posted' ? 'success' : 'warning' }}">
          {{ ucfirst($payment->status) }}
        </span>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-sm-4"><strong>Reference:</strong> {{ $payment->reference_no ?? '-' }}</div>
      <div class="col-sm-4"><strong>Amount:</strong> RM{{ number_format($payment->amount, 2) }}</div>
      <div class="col-sm-4"><strong>Bank/Cash Account:</strong> {{ $payment->account->name ?? '-' }}</div>
    </div>

    <div class="row mb-3">
      <div class="col-sm-12">
        <strong>Linked Source:</strong>
        @if($payment->source)
          @if($payment->type === 'receive')
            <a href="{{ route('accounting.sales.show', $payment->source_id) }}">Sales Invoice #{{ $payment->source_id }}</a>
          @else
            <a href="{{ route('accounting.purchases.show', $payment->source_id) }}">Purchase Bill #{{ $payment->source_id }}</a>
          @endif
        @else
          -
        @endif
      </div>
    </div>

    <hr>

    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0"><i class="fa fa-book"></i> Journal Entries</h6>
      <button class="btn btn-sm btn-outline-primary" id="viewJournalBtn">
        <i class="fa fa-eye"></i> View Journal
      </button>
    </div>
    <table id="journalEntriesTable" class="table table-sm table-bordered w-100">
      <thead class="table-light">
        <tr>
          <th>Account</th>
          <th class="text-end">Debit (RM)</th>
          <th class="text-end">Credit (RM)</th>
          <th>Memo</th>
        </tr>
      </thead>
      <tbody>
        @foreach($payment->journal?->entries ?? [] as $entry)
        <tr>
          <td>{{ $entry->account->name ?? '-' }}</td>
          <td class="text-end">{{ number_format($entry->debit, 2) }}</td>
          <td class="text-end">{{ number_format($entry->credit, 2) }}</td>
          <td>{{ $entry->memo }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <hr>

    <div>
      <h6><i class="fa fa-history"></i> Activity Logs</h6>
      <ul class="list-group list-group-flush">
        @forelse($activityLogs as $log)
          <li class="list-group-item small">
            <i class="fa fa-user text-primary"></i> {{ $log->user->name ?? 'System' }}
            — <strong>{{ $log->event }}</strong>
            on {{ $log->created_at->format('Y-m-d H:i:s') }}
          </li>
        @empty
          <li class="list-group-item text-muted">No activity recorded.</li>
        @endforelse
      </ul>
    </div>
  </div>
</div>

<!-- Modal for Journal Preview -->
<div class="modal fade" id="journalModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fa fa-book-open"></i> Journal Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <pre id="journalJson" class="bg-light p-3 rounded small"></pre>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
$(function() {
  $('#journalEntriesTable').DataTable({
    paging: false,
    searching: false,
    info: false
  });

  $('#viewJournalBtn').on('click', function() {
    const journal = @json($payment->journal);
    $('#journalJson').text(JSON.stringify(journal, null, 2));
    $('#journalModal').modal('show');
  });
});
@endsection
