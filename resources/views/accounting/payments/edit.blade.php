@extends('layouts.app')

@section('content')
<div class="card shadow-sm border-primary">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class="fa fa-edit"></i> Edit Payment</h5>
    <a href="{{ route('accounting.payments.index') }}" class="btn btn-light btn-sm">
      <i class="fa fa-arrow-left"></i> Back
    </a>
  </div>

  <div class="card-body">
    <form id="paymentForm" method="POST"
          action="{{ route('accounting.payments.update', $payment->id) }}"
          autocomplete="off">
      @csrf
      @method('PUT')

      <div class="row mb-3">
        <div class="col-sm-4">
          <label class="form-label">Type</label>
          <select name="type" id="type" class="form-select select2">
            <option value="receive" {{ $payment->type == 'receive' ? 'selected' : '' }}>Receive (Customer)</option>
            <option value="pay" {{ $payment->type == 'pay' ? 'selected' : '' }}>Pay (Supplier)</option>
          </select>
        </div>
        <div class="col-sm-4">
          <label class="form-label">Date</label>
          <input type="date" name="date" class="form-control"
                 value="{{ $payment->date->format('Y-m-d') }}">
        </div>
        <div class="col-sm-4">
          <label class="form-label">Reference No</label>
          <input type="text" name="reference_no" class="form-control"
                 value="{{ $payment->reference_no }}">
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-sm-4">
          <label class="form-label">Amount (RM)</label>
          <input type="number" step="0.01" name="amount" class="form-control text-end"
                 value="{{ $payment->amount }}">
        </div>
        <div class="col-sm-8">
          <label class="form-label">Bank / Cash Account</label>
          <select name="account_id" id="account_id" class="form-select select2">
            @foreach($accounts as $acc)
              <option value="{{ $acc->id }}"
                {{ $payment->account_id == $acc->id ? 'selected' : '' }}>
                {{ $acc->code }} — {{ $acc->name }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-sm-6">
          <label class="form-label">Linked Source</label>
          <select name="source_type" id="source_type" class="form-select select2">
            <option value="">None</option>
            @foreach($sourceTypes as $class => $label)
              <option value="{{ $class }}" {{ $payment->source_type == $class ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-sm-6">
          <label class="form-label">Source ID / Reference</label>
          <input type="text" name="source_id" class="form-control"
                 value="{{ $payment->source_id }}">
        </div>
      </div>

      <div class="text-end mt-4">
        <button type="submit" class="btn btn-primary">
          <i class="fa fa-save"></i> Save Changes
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('js')
$(function() {
  $('.select2').select2();

  $('#paymentForm').on('submit', function(e) {
    e.preventDefault();
    const form = this;
    swal.fire({
      title: 'Save changes?',
      text: 'The linked journal will be automatically rebuilt.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, save it!',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#6c757d'
    }).then((result) => {
      if (result.isConfirmed) form.submit();
    });
  });
});
@endsection
