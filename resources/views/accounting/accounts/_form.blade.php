<div class="form-group row m-1 @error('account_type_id') has-error @enderror">
	<label for="acct" class="col-form-label col-sm-2">Account Type : </label>
	<div class="col-auto">
		<select name="account_type_id" id="acct" class="form-select form-select-sm @error('account_type_id') is-invalid @enderror"></select>
		@error('account_type_id')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
		@enderror
	</div>
</div>
<div class="form-group row m-1 @error('account') has-error @enderror">
	<label for="acc" class="col-form-label col-sm-2">Account : </label>
	<div class="col-auto">
		<input type="text" name="account" value="{{ old('account', @$account->account) }}" id="acc" class="form-control form-control-sm @error('account') is-invalid @enderror" placeholder="Account">
		@error('account')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
		@enderror
	</div>
</div>
<div class="form-group row m-1 @error('code') has-error @enderror">
	<label for="cacc" class="col-form-label col-sm-2">Code : </label>
	<div class="col-auto">
		<input type="text" name="code" value="{{ old('code', @$account->code) }}" id="cacc" class="form-control form-control-sm @error('code') is-invalid @enderror" placeholder="Code">
		@error('code')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
		@enderror
	</div>
</div>
<div class="form-group row m-1 @error('description') has-error @enderror">
	<label for="desc" class="col-form-label col-sm-2">Description : </label>
	<div class="col-auto">
		<textarea name="description" id="desc" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Description">{{ old('description', @$account->description) }}</textarea>
		@error('description')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
		@enderror
	</div>
</div>
