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
		account: {
			validators: {
				notEmpty: {
					message: 'Please insert'
				},
			}
		},
		code: {
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


