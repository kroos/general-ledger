<div class="card">
  <div class="card-header"><h6 class="mb-0">Journal Entry</h6></div>
  <div class="card-body">
    <div class="row mb-3">
      <div class="form-group col-md-3 @error('date') has-error @enderror">
        <label class="form-label">Date</label>
        <input type="date" name="date" value="{{ old('date', @$variable->date) }}" class="form-control form-control-sm @error('date') is-invalid @enderror">
        @error('date')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
        @enderror
      </div>
      <div class="form-group col-md-4 @error('ledger_type_id') has-error @enderror">
        <label class="form-label">Ledger Type</label>
        <select name="ledger_type_id" id="ledger_type_id" class="form-select form-select-sm @error('ledger_type_id') is-invalid @enderror">
          <option value="">Please choose</option>
          @foreach($ledgerTypes as $id=>$name)
            <option value="{{ $id }}" {{ (old('ledger_type_id', @$variable->ledger_type_id) == $id)?'selected':NULL }}>{{ $name }}</option>
          @endforeach
        </select>
        @error('ledger_type_id')
          <div class="invalid-feedback">
            {{ $message }}
          </div>
        @enderror
      </div>
    </div>

    <div class="form-group col-md-8 my-3 @error('description') has-error @enderror">
      <label class="form-label">Description</label>
      <input type="text" name="description" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Optional journal description">
      @error('description')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
    </div>


    <div class="table-responsive @error('entries') has-error @enderror">
      <table class="form-group table table-sm table-bordered align-middle @error('entries') is-invalid @enderror">
        <thead class="table-light">
          <tr>
            <th>Account</th>
            <th class="text-end">Debit</th>
            <th class="text-end">Credit</th>
            <th>Description</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="entries"></tbody>
      </table>
      @error('entries')
        <div class="invalid-feedback">
          {{ $message }}
        </div>
      @enderror
      <button type="button" id="entry_add" class="btn btn-sm btn-outline-primary my-1">
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
