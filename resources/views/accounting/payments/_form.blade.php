<div class="card border-success">
  <div class="card-header bg-success text-white">
    <i class="fa fa-credit-card"></i> New Payment
  </div>

  <div class="card-body">
    <div class="row mb-3">
      <div class="col-sm-3">
        <label>Type</label>
        <select name="type" id="type" class="form-select form-select-sm">
          <option value="receive">Receive Payment (Customer)</option>
          <option value="make">Make Payment (Supplier)</option>
        </select>
      </div>
      <div class="col-sm-3">
        <label>Date</label>
        <input type="date" name="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
      </div>
      <div class="col-sm-3">
        <label>Reference</label>
        <input type="text" name="reference_no" class="form-control form-control-sm">
      </div>
      <div class="col-sm-3">
        <label>Amount</label>
        <input type="number" step="0.01" name="amount" class="form-control form-control-sm">
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-sm-6">
        <label>Bank / Cash Account</label>
        <select name="account_id" class="form-select form-select-sm select2">
          <option value="">Choose account...</option>
          @foreach($accounts as $k1)
            <option value="{{ $k1->id }}">{{ $k1->name }}</option>
          @endforeach
        </select>
      </div>

      <div id="invoice_select_wrap" class="col-sm-6 source-select">
        <label>Apply to Sales Invoice</label>
        <select name="source_id" class="form-select form-select-sm select2">
          <option value="">Select invoice...</option>
          @foreach($salesInvoices as $inv)
            <option value="{{ $inv->id }}" data-source_type="App\Models\Accounting\SalesInvoice">
              Invoice #{{ $inv->id }} - RM{{ number_format($inv->total,2) }}
            </option>
          @endforeach
        </select>
        <input type="hidden" name="source_type" value="App\Models\Accounting\SalesInvoice">
      </div>

      <div id="bill_select_wrap" class="col-sm-6 source-select d-none">
        <label>Apply to Purchase Bill</label>
        <select name="source_id" class="form-select form-select-sm select2">
          <option value="">Select bill...</option>
          @foreach($purchaseBills as $bill)
            <option value="{{ $bill->id }}" data-source_type="App\Models\Accounting\PurchaseBill">
              Bill #{{ $bill->id }} - RM{{ number_format($bill->total,2) }}
            </option>
          @endforeach
        </select>
        <input type="hidden" name="source_type" value="App\Models\Accounting\PurchaseBill">
      </div>
    </div>
  </div>

  <div class="card-footer text-end">
    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Record Payment</button>
    <a href="{{ route('accounting.payments.index') }}" class="btn btn-secondary">Cancel</a>
  </div>
</div>
