<div class="card-body">
	<div class="row mb-3">

		<div class="col-sm-3 @error('type') has-error @enderror">
			<label for="type" class="col-form-label">Type</label>
			<select name="type" id="type" class="form-select form-select-sm select2 @error('type') is-invalid @enderror">
				<option value="">Please Choose</option>
				<option value="receive" {{ (old('type', @$payment->type)=='receive')?'selected="selected"':NULL }}>Receive Payment (Customer)</option>
				<option value="make" {{ (old('type', @$payment->type)=='make')?'selected="selected"':NULL }}>Make Payment (Supplier)</option>
			</select>
			@error('type')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>
		<div class="col-sm-3 @error('date') has-error @enderror">
			<label for="date" class="col-form-label">Date</label>
			<input type="date" name="date" id="date" value="{{ old('date', @$payment->date?->format('Y-m-d')) }}" class="form-control form-control-sm @error('date') is-invalid @enderror" value="{{ old('date') }}">
			@error('date')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>
		<div class="col-sm-3 @error('name') has-error @enderror">
			<label for="reference_no" class="col-form-label">Reference</label>
			<input type="text" name="reference_no" value="{{ old('reference_no',  @$payment->reference_no) }}" id="reference_no" class="form-control form-control-sm @error('reference_no') is-invalid @enderror" value="{{ old('reference_no') }}">
			@error('reference_no')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>
		<div class="col-sm-3 @error('amount') has-error @enderror">
			<label for="amount" class="col-form-label">Amount</label>
			<input type="number" step="0.01" name="amount" value="{{ old('amount',  @$payment->amount) }}" id="amount" class="form-control form-control-sm @error('amount') is-invalid @enderror">
			@error('amount')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>
	</div>

	<div class="row mb-3">
		<div class="col-sm-6 @error('account_id') has-error @enderror">
			<label for="account_id" class="col-form-label">Bank / Cash Account</label>
			<select name="account_id" id="account_id" class="form-select form-select-sm @error('account_id') is-invalid @enderror"></select>
			@error('account_id')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>

		<div id="wrap" class="col-sm-6">
		</div>
	</div>
</div>
