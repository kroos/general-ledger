<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Accounting Defaults
	|--------------------------------------------------------------------------
	|
	| Centralized configuration for journals, accounts, and transaction
	| references to keep accounting logic consistent across the system.
	|
	*/

	'defaults' => [
		'journal' => [
			'tax_account' => env('ACCOUNTING_TAX_ACCOUNT', 1),
			'revenue_account' => env('ACCOUNTING_REVENUE_ACCOUNT', 2),
			'expense_account' => env('ACCOUNTING_EXPENSE_ACCOUNT', 3),
			'cash_account' => env('ACCOUNTING_CASH_ACCOUNT', 4),
		],
	],

];
