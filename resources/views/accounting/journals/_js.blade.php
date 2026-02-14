@php
	$items = @$journal?->hasmanyjournalentries()?->get(['id', 'date', 'account_id', 'description', 'no_reference', 'ledger_id', 'debit', 'credit']);
	$itemsArray = $items?->toArray()??[];
	$oldItemsValue = old('journals', $itemsArray);
@endphp

window.data = {
	route: {
		getLedgers: '{{ route('getLedgers') }}',
		getAccounts: '{{ route('getAccounts') }}',
	},
	url: {
		journals: `{{ url('journals') }}`,
	},
	old: {
		ledgerid: @json(old('ledger_id', @$journal->ledger_id)),
		oldJournals: @json($oldItemsValue)
	},
	errors: @json($errors->toArray()),
};

