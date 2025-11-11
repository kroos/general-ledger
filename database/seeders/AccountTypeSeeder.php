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
		\App\Models\Accounting\AccountType::create(['account_type' => 'Asset']);
		\App\Models\Accounting\AccountType::create(['account_type' => 'Current Asset']);
		\App\Models\Accounting\AccountType::create(['account_type' => 'Equity']);
		\App\Models\Accounting\AccountType::create(['account_type' => 'Liability']);
	}
}
