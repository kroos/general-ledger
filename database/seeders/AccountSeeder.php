<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		\App\Models\Accounting\Account::create([
			'account_type_id' => 1,
			'code' => 1000,
			'account' => 'Cash',
		]);
		\App\Models\Accounting\Account::create([
			'account_type_id' => 1,
			'code' => 1100,
			'account' => 'Accounts Receivable',
		]);
		\App\Models\Accounting\Account::create([
			'account_type_id' => 1,
			'code' => 1200,
			'account' => 'Assets',
		]);
		\App\Models\Accounting\Account::create([
			'account_type_id' => 5,
			'code' => 1300,
			'account' => 'Sales Revenue',
		]);
		\App\Models\Accounting\Account::create([
			'account_type_id' => 2,
			'code' => 2000,
			'account' => 'Current Assets',
		]);
		\App\Models\Accounting\Account::create([
			'account_type_id' => 3,
			'code' => 3000,
			'account' => 'Equity',
		]);
		\App\Models\Accounting\Account::create([
			'account_type_id' => 4,
			'code' => 4000,
			'account' => 'Accounts Payable',
		]);
		\App\Models\Accounting\Account::create([
			'account_type_id' => 6,
			'code' => 4100,
			'account' => 'Cost of Goods Sold',
		]);
		\App\Models\Accounting\Account::create([
			'account_type_id' => 6,
			'code' => 4200,
			'account' => 'Operating Expenses',
		]);
	}
}
