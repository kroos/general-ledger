<div class="card-body">
	<div class="row mb-3">

		<div class="col-sm-3 @error('type') has-error @enderror">
			<label for="type" class="col-form-label">Type</label>
			<select name="type" id="type" class="form-select form-select-sm select2 @error('type') is-invalid @enderror">
				<option value="">Please Choose</option>
				<option value="receive" {{ (old('type')=='receive')?'selected':NULL }}>Receive Payment (Customer)</option>
				<option value="make" {{ (old('type')=='make')?'selected':NULL }}>Make Payment (Supplier)</option>
			</select>
			@error('type')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>
		<div class="col-sm-3 @error('date') has-error @enderror">
			<label for="date" class="col-form-label">Date</label>
			<input type="date" name="date" id="date" class="form-control form-control-sm @error('date') is-invalid @enderror" value="{{ old('date') }}">
			@error('date')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>
		<div class="col-sm-3 @error('name') has-error @enderror">
			<label for="reference_no" class="col-form-label">Reference</label>
			<input type="text" name="reference_no" id="reference_no" class="form-control form-control-sm @error('reference_no') is-invalid @enderror" value="{{ old('reference_no') }}">
			@error('reference_no')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
		</div>
		<div class="col-sm-3 @error('amount') has-error @enderror">
			<label for="amount" class="col-form-label">Amount</label>
			<input type="number" step="0.01" name="amount" value="{{ old('amount') }}" id="amount" class="form-control form-control-sm @error('amount') is-invalid @enderror">
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

		<div id="invoice_select_wrap" class="col-sm-6 source-select @error('source_id') has-error @enderror">
			<label for="source_id" class="col-form-label">Apply to Sales Invoice</label>
			<select name="source_id" id="source_id" class="form-select form-select-sm select2 @error('source_id') is-invalid @enderror">
				<option value="">Select invoice...</option>
				@foreach($salesInvoices as $inv)
				<option value="{{ $inv->id }}" data-source_type="App\Models\Accounting\SalesInvoice" {{ (old('source_id')==$inv->id)?'selected':NULL }}>
					Invoice #{{ $inv->id }} - RM {{ number_format($inv->total,2) }}
				</option>
				@endforeach
			</select>
			@error('source_id')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
			<input type="hidden" name="source_type" value="App\Models\Accounting\SalesInvoice" >
		</div>

		<div id="bill_select_wrap" class="col-sm-6 source-select d-none @error('source_id') has-error @enderror">
			<label for="source" class="col-form-label">Apply to Purchase Bill</label>
			<select name="source_id" id="source" class="form-select form-select-sm select2 @error('source_id') is-invalid @enderror">
				<option value="">Select bill...</option>
				@foreach($purchaseBills as $bill)
				<option value="{{ $bill->id }}" data-source_type="App\Models\Accounting\PurchaseBill" {{ (old('source_id')==$bill->id)?'selected':NULL }}>
					Bill #{{ $bill->id }} - RM{{ number_format($bill->total,2) }}
				</option>
				@endforeach
			</select>
			@error('source_id')
			<div class="invalid-feedback">
				{{ $message }}
			</div>
			@enderror
			<input type="hidden" name="source_type" value="App\Models\Accounting\PurchaseBill">
		</div>
	</div>
</div>
