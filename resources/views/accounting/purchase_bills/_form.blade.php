<div class="row g-3">
  <div class="col-md-3">
    <label class="form-label">Date</label>
    <input type="date" name="date" value="{{ old('date', optional($invoice)->date?->format('Y-m-d')) }}" class="form-control form-control-sm" required>
  </div>
  <div class="col-md-3">
    <label class="form-label">Reference No.</label>
    <input type="text" name="reference_no" value="{{ old('reference_no', $invoice->reference_no ?? '') }}" class="form-control form-control-sm" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">supplier</label>
    <select name="supplier_id" id="supplierSelect" class="form-select form-select-sm">
      <option value="">Select supplier</option>
      {{-- Populate dynamically if supplier table exists --}}
    </select>
  </div>

  <div class="col-12 mt-3">
    <table class="table table-sm table-bordered align-middle" id="itemsTable">
      <thead class="table-light">
        <tr>
          <th style="width: 30%">Account</th>
          <th>Description</th>
          <th style="width: 20%">Amount</th>
          <th style="width: 5%"><button type="button" class="btn btn-sm btn-success" id="addRow"><i class="fa fa-plus"></i></button></th>
        </tr>
      </thead>
      <tbody>
        @foreach(old('items', $invoice->items ?? [['account_id'=>'','description'=>'','amount'=>'']]) as $i => $item)
        <tr>
          <td>
            <select name="items[{{ $i }}][account_id]" class="form-select form-select-sm select2" required>
              <option value="">Select Account</option>
              @foreach($accounts as $id => $name)
                <option value="{{ $id }}" {{ $id == ($item['account_id'] ?? null) ? 'selected' : '' }}>{{ $name }}</option>
              @endforeach
            </select>
          </td>
          <td><input type="text" name="items[{{ $i }}][description]" value="{{ $item['description'] ?? '' }}" class="form-control form-control-sm"></td>
          <td><input type="number" name="items[{{ $i }}][amount]" value="{{ $item['amount'] ?? '' }}" step="0.01" class="form-control form-control-sm text-end" required></td>
          <td class="text-center"><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fa fa-times"></i></button></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="col-md-3 ms-auto">
    <label class="form-label">Subtotal</label>
    <input type="number" step="0.01" name="subtotal" id="subtotal" value="{{ old('subtotal', $invoice->subtotal ?? 0) }}" class="form-control form-control-sm text-end" readonly>
  </div>
  <div class="col-md-3">
    <label class="form-label">Tax</label>
    <input type="number" step="0.01" name="tax" id="tax" value="{{ old('tax', $invoice->tax ?? 0) }}" class="form-control form-control-sm text-end">
  </div>
  <div class="col-md-3">
    <label class="form-label">Total</label>
    <input type="number" step="0.01" name="total" id="total" value="{{ old('total', $invoice->total ?? 0) }}" class="form-control form-control-sm text-end" readonly>
  </div>
</div>
