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

