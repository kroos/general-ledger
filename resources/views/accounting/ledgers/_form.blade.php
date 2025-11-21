<div class="form-group row m-1 @error('account_type_id') has-error @enderror">
	<label for="acct" class="col-form-label col-sm-2">Account Type : </label>
	<div class="col-6 my-auto">
		<select name="account_type_id" id="acct" class="form-select form-select-sm @error('account_type_id') is-invalid @enderror"></select>
		@error('account_type_id')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
		@enderror
	</div>
</div>

<div class="form-group row m-1 @error('ledger') has-error @enderror">
	<label for="led" class="col-form-label col-sm-2">Ledger : </label>
	<div class="col-auto my-auto">
		<input type="text" name="ledger" value="{{ old('ledger', @$ledger->ledger) }}" id="led" class="form-control form-control-sm @error('ledger') is-invalid @enderror" placeholder="Ledger">
		@error('ledger')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
		@enderror
	</div>
</div>

<div class="form-group row m-1 @error('description') has-error @enderror">
	<label for="desc" class="col-form-label col-sm-2">Description : </label>
	<div class="col-auto my-auto">
		<textarea name="description" id="desc" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Description">{{ old('description', @$ledger->description) }}</textarea>
		@error('description')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
		@enderror
	</div>
</div>
