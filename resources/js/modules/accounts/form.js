const { route, url, old } = window.data;
$('#acct').select2({
	...config.select2,
	ajax: {
		url: route.getAccountTypes,
		type: 'GET',
		dataType: 'json',
		delay: 250,											// Delay to reduce server requests
		data: function (params) {
			return {
				search: params.term
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
if(old.accounttypeid){
	$.ajax({
		url: route.getAccountTypes,
		dataType: 'json',
		data: {
			id: old.accounttypeid
		},
	}).then(data => {
		const item = Array.isArray(data) ? data[0] : data;	// change object to array
		if (!item) return;
		const option = new Option(item.account_type, item.id, true, true);
		$('#acct').append(option).trigger('change');
	});
}
