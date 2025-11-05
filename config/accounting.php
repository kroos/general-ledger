<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Default Accounting Configuration
	|--------------------------------------------------------------------------
	|
	| You can define default account IDs and ledger settings here.
	| These values are used by JournalService when auto-posting
	| transactions such as sales, purchases, and payments.
	|
	*/

	'defaults' => [
				'cash_account_id'        => 1, // Cash / Bank
				'accounts_receivable_id' => 2, // A/R
				'sales_revenue_id'       => 3, // Sales
				'accounts_payable_id'    => 4, // A/P
				'expense_default_id'     => 5, // Expense
	],
	'accounts' => [
				'sales_revenue'      => 4,  // Revenue account ID
				'accounts_receivable'=> 6,  // AR
				'accounts_payable'   => 7,  // AP
				'cash'               => 1,  // Cash / Bank
	],

];
