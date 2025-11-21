<div class="form-group row m-1 @error('ledger_id') has-error @enderror">
	<label for="ledg" class="col-form-label col-sm-2">Ledger : </label>
	<div class="col-sm-6 my-auto">
		<select name="ledger_id" id="ledg" class="form-select form-select-sm @error('ledger_id') is-invalid @enderror"></select>
		@error('ledger_id')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
		@enderror
	</div>
</div>

<div class="form-group row m-1 @error('date') has-error @enderror">
	<label for="date" class="col-form-label col-sm-2">Date : </label>
	<div class="col-sm-auto my-auto">
		<input type="text" name="date" value="{{ old('date', @$journal?->date->format('Y-m-d')) }}" id="date" class="form-control form-control-sm @error('date') is-invalid @enderror" placeholder="Date">
		@error('date')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
		@enderror
	</div>
</div>

<div class="form-group row m-1 @error('no_reference') has-error @enderror">
	<label for="noref" class="col-form-label col-sm-2">No Reference : </label>
	<div class="col-sm-auto my-auto">
		<input type="text" name="no_reference" value="{{ old('no_reference', @$journal->no_reference) }}" id="noref" class="form-control form-control-sm @error('no_reference') is-invalid @enderror" placeholder="No Reference">
		@error('no_reference')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
		@enderror
	</div>
</div>

<div class="form-group row m-1 @error('description') has-error @enderror">
	<label for="desc" class="col-form-label col-sm-2">Description : </label>
	<div class="col-sm-auto my-auto">
		<textarea name="description" id="desc" class="form-control form-control-sm @error('description') is-invalid @enderror" placeholder="Description">{{ old('description', @$journal->description) }}</textarea>
		@error('description')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
		@enderror
	</div>
</div>

<div>
<table class="table table-sm table-hover @error('journals') has-error is-invalid @enderror">
	<thead>
		<tr>
			<th>Date</th>
			<th>Account</th>
			<th>Description</th>
			<th>No Reference</th>
			<th>Ledger</th>
			<th>Debit</th>
			<th>Credit</th>
			<th>
				<button type="button" class="btn btn-sm btn-outline-primary" id="journal_add"><i class="fa-solid fa-folder-plus"></i></button>
			</th>
		</tr>
	</thead>
	<tbody id="journals_wrap">
	</tbody>
</table>
@error('journals')
	<div class="invalid-feedback">
		{{ $message }}
	</div>
@enderror
</div>
