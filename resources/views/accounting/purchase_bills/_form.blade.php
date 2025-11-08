	<div class="row mb-2">
		<div class="col-sm-4 @error('date') has-error @enderror">
			<label class="form-label">Date:</label>
			<input type="date" name="date" id="date" value="{{ old('date', \Carbon\Carbon::parse(@$bill->date)->format('Y-m-d')) }}" class="form-control form-control-sm @error('date') is-invalid @enderror">
			@error('date')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>
		<div class="col-sm-4 @error('reference_no') has-error @enderror">
			<label class="form-label">Reference No:</label>
			<input type="text" name="reference_no" id="reference_no" value="{{ old('reference_no', @$bill->reference_no) }}" class="form-control form-control-sm @error('reference_no') is-invalid @enderror" value="{{ old('reference_no') }}">
			@error('reference_no')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>
		<div class="col-sm-4 @error('supplier_id') has-error @enderror">
			<label for="cust" class="form-label">Supplier ID:</label>
			<select name="supplier_id" id="cust" class="form-select form-select-sm @error('supplier_id') is-invalid @enderror"></select>
			@error('supplier_id')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>
	</div>

	<hr>
	<div class="d-flex justify-content-between align-items-center mb-2">
		<h6 class="m-0"><i class="fa fa-list"></i> Items</h6>
		<button type="button" id="item_add" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add Item</button>
	</div>
	<div id="items_wrap" class="@error('items') is-invalid @enderror">

	</div>
	@error('items')
	<div class="invalid-feedback">
		{{ $message }}
	</div>
	@enderror

	<hr>
	<div class="row justify-content-end">
		<div class="col-sm-4">
			<div class="input-group input-group-sm mb-2 @error('tax_rate_percent') has-error @enderror">
				<span class="input-group-text">Tax Rate %</span>
				<input type="number" step="0.1" name="tax_rate_percent" id="tax_rate_percent" value="{{ old('tax_rate_percent', @$bill->tax_rate_percent) }}" class="form-control form-control-sm @error('tax_rate_percent') is-invalid @enderror" min="0" max="100">
				@error('tax_rate_percent')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</div>

			<div class="input-group input-group-sm mb-1">
				<span class="input-group-text">Subtotal</span>
				<input type="text" name="subtotal" value="{{ old('subtotal', @$bill->subtotal) }}" class="form-control text-end" readonly>
			</div>
			<div class="input-group input-group-sm mb-1">
				<span class="input-group-text">Tax</span>
				<input type="text" name="tax" value="{{ old('tax', @$bill->tax) }}" class="form-control text-end" readonly>
			</div>
			<div class="input-group input-group-sm">
				<span class="input-group-text bg-success text-white fw-bold">Total</span>
				<input type="text" name="total_amount" value="{{ old('total_amount', @$bill->total_amount) }}" class="form-control text-end fw-bold" readonly>
			</div>
		</div>
	</div>


