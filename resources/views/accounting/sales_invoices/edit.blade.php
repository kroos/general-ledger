@extends('layouts.app')

@section('content')
<div class="col-sm-12">
	<form method="POST" action="{{ route('accounting.sales-invoices.update', $invoice) }}" accept-charset="UTF-8" id="form" autocomplete="off" class="" enctype="multipart/form-data">
		@csrf
		@method('PATCH')
		<div class="card border-success">
			<div class="card-header bg-success text-white">
				<i class="fa fa-edit"></i> Edit Sales Invoice
			</div>

			<div class="card-body">
				@include('accounting.sales_invoices._form', ['invoice' => $invoice])
			</div>
			<div class="card-footer text-end">
				<button type="submit" name="action" value="draft" class="btn btn-secondary"><i class="fa fa-save"></i> Update & Save Draft</button>
				<button type="submit" name="action" value="post" class="btn btn-success"><i class="fa fa-check"></i> Update & Post Now</button>
				<a href="{{ route('accounting.sales-invoices.index') }}" class="btn btn-secondary">Cancel</a>
			</div>
		</div>
	</form>
</div>
@endsection

@section('js')
recalcTotals();
$(document).on('input', '#tax_rate_percent', recalcTotals);

$("#items_wrap").remAddRow({
	addBtn: "#item_add",
	maxFields: 50,
	removeSelector: ".item_remove",
	fieldName: "items",
	rowIdPrefix: "item",
	rowTemplate: (i, name) => `
		<div class="row item-row border-bottom py-1 align-items-center" id="item_${i}">
			<div class="col-sm-4 @error('items.*.account_id') has-error @enderror">
				<select name="${name}[${i}][account_id]" id="account_${i}" class="form-select form-select-sm @error('items.*.account_id') is-invalid @enderror"></select>
				@error('items.*.account_id')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror
			</div>
			<div class="col-sm-3 @error('items.*.description') has-error @enderror">
				<input type="text" name="${name}[${i}][description]" value="{{ old('items.*.description') }}" class="form-control form-control-sm @error('items.*.description') is-invalid @enderror" placeholder="Description">
				@error('items.*.description')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror
			</div>
			<div class="col-sm-1 @error('items.*.quantity') has-error @enderror">
				<input type="number" step="0.1" name="${name}[${i}][quantity]" value="{{ old('items.*.quantity', 1) }}" class="form-control form-control-sm quantity @error('items.*.quantity') is-invalid @enderror">
				@error('items.*.quantity')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror
			</div>
			<div class="col-sm-2 @error('items.*.unit_price') has-error @enderror">
				<input type="number" step="0.01" name="${name}[${i}][unit_price]" value="{{ old('items.*.unit_price') }}" class="form-control form-control-sm unit_price @error('items.*.unit_price') is-invalid @enderror">
				@error('items.*.unit_price')
				<div class="invalid-feedback">
					{{ $message }}
				</div>
				@enderror
			</div>
			<div class="col-sm-1 text-end">
				<input type="number" step="0.1" name="${name}[${i}][amount]" value="{{ old('items.*.amount') }}" class="form-control form-control-sm amount" readonly>
			</div>
			<div class="col-sm-1 text-center">
				<button type="button" class="btn btn-sm btn-danger item_remove"><i class="fa fa-times"></i></button>
			</div>
		</div>
	`,
	onAdd: (i, row) => {
		console.log("Items added:", `item_${i}`, row);

		$(`#account_${i}`).select2({
			placeholder: 'Select Account',
			width: '100%',
			allowClear: true,
			closeOnSelect: true,
			theme: 'bootstrap-5',
			ajax: {
				url: '{{ route('getAccounts') }}',
				type: 'GET',
				dataType: 'json',
				delay: 250, // prevents excessive requests while typing
				data: function (params) {
					return {
						_token: '{{ csrf_token() }}',
						search: params.term // optional if you want filtering
					};
				},
				processResults: function (data) {
					// API returns plain array, so map it to Select2 format
					return {
						results: data.map(item => ({
							id: item.id,
							text: item.code + ' ' + item.name
						}))
					};
				}
			}
		});

		// Bind input events to the new row
		$(`#item_${i} .quantity, #item_${i} .unit_price`).on('input', recalcTotals);

		// Trigger calculation after adding new row
		setTimeout(recalcTotals, 100);

	},
	onRemove: (i) => {
		console.log("Items removed:", `item_${i}`);
		recalcTotals();
	},
});

function recalcTotals() {
	let subtotal = 0;

	$('#items_wrap .item-row').each(function() {
		const qty = parseFloat($(this).find('.quantity').val()) || 0;
		const price = parseFloat($(this).find('.unit_price').val()) || 0;
		const amt = qty * price;

		$(this).find('.amount').val(amt.toFixed(2));
		subtotal += amt;
	});

	const subtotalFixed = subtotal.toFixed(2);
	const taxRate = parseFloat($('#tax_rate_percent').val()) / 100 || 0;
	const tax = (subtotal * taxRate).toFixed(2);
	const total = (parseFloat(subtotal) + parseFloat(tax)).toFixed(2);

	$('[name="subtotal"]').val(subtotalFixed);
	$('[name="tax"]').val(tax);
	$('[name="total_amount"]').val(total);
}

// Confirm before submit
$('#form').on('submit', function(e) {
	e.preventDefault();
	swal.fire({
		title: 'Save as draft?',
		text: 'You can post this invoice later.',
		icon: 'question',
		showCancelButton: true,
		confirmButtonText: 'Yes, save it',
	}).then(res => {
		if (res.isConfirmed) this.submit();
	});
});

// restore from failed validation
@php
$items = $invoice->items()->get(['account_id', 'description', 'quantity', 'unit_price', 'amount']);
$itemsArray = $items->toArray();
$oldItemsValue = old('items', $itemsArray);
@endphp

const oldItems = @json($oldItemsValue);
if (oldItems.length > 0) {
	oldItems.forEach(function (item, i) {
		$("#item_add").trigger('click');
		const $items = $("#items_wrap").children().eq(i);
		const $account = $items.find(`select[name="items[${i}][account_id]"]`);

		if (item.account_id) {
			$.ajax({
				url: `{{ route('getAccounts') }}`,
				dataType: 'json',
				data: {
					id: `${item.account_id}`,
					_token: `{{ csrf_token() }}`,
				}
			}).then(data => {
				const found = data.find(d => String(d.id) === String(item.account_id));
				if (found) {
					const option = new Option(found.code +' '+ found.name, found.id, true, true);
					$account.append(option).trigger('change');
				}
			});
		}
		$items.find(`input[name="items[${i}][unit_price]"]`).val(item.unit_price || '');
		$items.find(`input[name="items[${i}][quantity]"]`).val(item.quantity || '');
		$items.find(`input[name="items[${i}][description]"]`).val(item.description || '');
		$items.find(`input[name="items[${i}][amount]"]`).val(item.amount || '');
	});
}

@endsection
