window.data = {
	route: {
		getAccountTypes: '{{ route('getAccountTypes') }}',
	},
	url: {
	},
	old: {
		accounttypeid: @json(old('account_type_id', @$account->account_type_id)),
	},
};
