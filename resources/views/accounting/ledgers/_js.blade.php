window.data = {
	route: {
		getAccountTypes: '{{ route('getAccountTypes') }}',
	},
	url: {
	},
	old: {
		accounttypeid: @json(old('account_type_id', @$ledger->account_type_id)),
	},
};

