<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts')->insert([
            ['code'=>'1000','name'=>'Cash','type'=>'asset'],
            ['code'=>'1100','name'=>'Accounts Receivable','type'=>'asset'],
            ['code'=>'2000','name'=>'Accounts Payable','type'=>'liability'],
            ['code'=>'3000','name'=>'Equity','type'=>'equity'],
            ['code'=>'4000','name'=>'Sales Revenue','type'=>'income'],
            ['code'=>'5000','name'=>'Cost of Goods Sold','type'=>'expense'],
            ['code'=>'5100','name'=>'Operating Expenses','type'=>'expense'],
        ]);
    }
}
