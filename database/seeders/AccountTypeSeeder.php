<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$types = [
			'Asset' => [
				['code' => 1000, 'account' => 'Cash'],
				['code' => 1100, 'account' => 'Accounts Receivable'],
				['code' => 1200, 'account' => 'Assets'],
			],
			'Current Asset' => [
				['code' => 2000, 'account' => 'Current Assets'],
			],
			'Equity' => [
				['code' => 3000, 'account' => 'Equity'],
			],
			'Liability' => [
				['code' => 4000, 'account' => 'Accounts Payable'],
			],
			'Income' => [
				['code' => 1300, 'account' => 'Sales Revenue'],
			],
			'Expense' => [
				['code' => 4100, 'account' => 'Cost Of Goods Sold'],
				['code' => 4200, 'account' => 'Operating Expenses'],
			],
		];

		foreach ($types as $type => $accounts) {
			// FirstOrCreate prevents duplicate AccountType
			$accountType = \App\Models\Accounting\AccountType::firstOrCreate(
				['account_type' => $type]
			);

			foreach ($accounts as $acc) {
				// FirstOrCreate prevents duplicate Accounts for each type
				$accountType->hasmanyaccount()->firstOrCreate(
					['code' => $acc['code']],  // unique identifier
					['account' => $acc['account']]
			 );
			}
		}
	}
}
