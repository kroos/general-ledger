<div class="card">
  <div class="card-header"><h6 class="mb-0">Journal Entry</h6></div>
  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-3">
        <label class="form-label">Date</label>
        <input type="date" name="date" value="{{ now()->toDateString() }}" class="form-control form-control-sm">
      </div>
      <div class="col-md-4">
        <label class="form-label">Ledger Type</label>
        <select name="ledger_type_id" id="ledger_type_id" class="form-select form-select-sm">
          @foreach($ledgerTypes as $id=>$name)
            <option value="{{ $id }}">{{ $name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="col-md-8 mt-3">
      <label class="form-label">Description</label>
      <input type="text" name="description" class="form-control form-control-sm" placeholder="Optional journal description">
    </div>


    <div class="table-responsive">
      <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th>Account</th>
            <th class="text-end">Debit</th>
            <th class="text-end">Credit</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="entries"></tbody>
      </table>
      <button type="button" id="addRow" class="btn btn-sm btn-outline-primary">
        <i class="fa fa-plus"></i> Add Row
      </button>
    </div>

    <div class="mt-3 d-flex gap-2">
      <button name="post_now" value="1" class="btn btn-success">
        <i class="fa fa-check"></i> Save & Post
      </button>
      <button type="submit" class="btn btn-secondary">
        <i class="fa fa-save"></i> Save Draft
      </button>
      <a href="{{ route('journals.index') }}" class="btn btn-light">Cancel</a>
    </div>
  </div>
</div>
