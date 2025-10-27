<?php

namespace Database\Seeders;

use App\Models\YesNoOption;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class YesNoOptionSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		YesNoOption::create(
				[
					'option' => 'Yes',
					'value' => 1,
				]);
		YesNoOption::create(
				[
					'option' => 'No',
					'value' => 0,
				]);
	}
}
