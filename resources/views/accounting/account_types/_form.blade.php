<div class="form-group row m-1 @error('account_type') has-error @enderror">
	<label for="id" class="col-form-label col-sm-2">Account Type : </label>
	<div class="col-auto my-auto">
		<input type="text" name="account_type" value="{{ old('account_type', @$accountType->account_type) }}" id="id" class="form-control form-control-sm col-sm-12 @error('account_type') is-invalid @enderror" placeholder="Account Type">
		@error('account_type')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
		@enderror
	</div>
</div>

<div class="form-group row m-1 @error('description') has-error @enderror">
	<label for="desc" class="col-form-label col-sm-2">Description : </label>
	<div class="col-auto my-auto">
		<textarea name="description" id="desc" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Description">{{ old('description', @$accountType->description) }}</textarea>
		@error('description')
		<div class="invalid-feedback">
			{{ $message }}
		</div>
	@enderror
	</div>
</div>
