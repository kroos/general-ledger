			<div class="mb-3 @error('code') has-error @enderror">
				<label for="code" class="col-form-label">Code</label>
				<input name="code" id="code" value="{{ old('old', @$account->code) }}" class="form-control form-control-sm @error('code') is-invalid @enderror">
				@error('code')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</div>
			<div class="mb-3 @error('name') has-error @enderror">
				<label for="name" class="col-form-label">Name</label>
				<input name="name" id="name" value="{{ old('name', @$account->name) }}" class="form-control form-control-sm @error('name') is-invalid @enderror" required>
				@error('name')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</div>
			<div class="mb-3 @error('type') has-error @enderror">
				<label for="type">Type</label>
				<select name="type" id="type" class="form-select form-select-sm @error('type') is-invalid @enderror" required>
					<option value="">Select Type</option>
					@foreach(['asset','liability','equity','income','expense'] as $type)
					<option value="{{ $type }}" {{ (old('type', @$account->type) == $type)?'selected':NULL }}>{{ ucfirst($type) }}</option>
					@endforeach
				</select>
			</div>
			<div class="mb-3 @error('parent_id') has-error @enderror">
				<label for="pid">Parent Account</label>
				<select name="parent_id" id="pid" class="form-select form-select-sm @error('parent_id') has-error @enderror"></select>
				@error('parent_id')
					<div class="invalid-feedback">
						{{ $message }}
					</div>
				@enderror
			</div>
			<div class="mb-3">
				<button class="btn btn-success btn-sm"><i class="fa fa-save"></i> Save</button>
				<a href="{{ route('accounts.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
			</div>
