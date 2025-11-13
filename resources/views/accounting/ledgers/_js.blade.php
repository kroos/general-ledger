//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$('#acct').select2({
	theme: 'bootstrap-5',
	placeholder: 'Please choose',
	allowClear: true,
	closeOnSelect: true,
	width: '100%',
	ajax: {
		url: '{{ route('getAccountTypes') }}',
		type: 'GET',
		dataType: 'json',
		delay: 250,											// Delay to reduce server requests
		data: function (params) {
			return {
				_token: '{!! csrf_token() !!}',
				search: params.term,				// Search query
			}
		},
		processResults: function (data) {
			return {
				results: data.map(function(item) {
					return {
						id: item.id,
						text: item.account_type,
						raw: item
					}
				})
			};
		}
	},
});
@if(old('account_type_id', @$ledger->account_type_id))
	$.ajax({
		url: `{{ route('getAccountTypes') }}`,
		data: {
			id: `{{ old('account_type_id', @$ledger->account_type_id) }}`
		},
		dataType: 'json'
	}).then(data => {
		const item = Array.isArray(data) ? data[0] : data;	// change object to array
		if (!item) return;
		const option = new Option(item.account_type, item.id, true, true);
		$('#acct').append(option).trigger('change');
	});
@endif
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// form validation
$('#form').bootstrapValidator({
	fields: {
		account_type_id: {
			validators: {
				notEmpty: {
					message: 'Please insert'
				},
			}
		},
		ledger: {
			validators: {
				notEmpty: {
					message: 'Please insert'
				},
			}
		},
		description: {
			validators: {
			}
		},
	}
});

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
