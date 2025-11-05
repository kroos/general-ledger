<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LedgerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ledger_types')->insert([
            ['name'=>'General','slug'=>'general','is_system'=>true],
            ['name'=>'Sales','slug'=>'sales','is_system'=>true],
            ['name'=>'Purchases','slug'=>'purchases','is_system'=>true],
        ]);
    }
}
