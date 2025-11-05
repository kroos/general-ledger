<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cash  = DB::table('accounts')->where('code','1000')->value('id');
        $ar    = DB::table('accounts')->where('code','1100')->value('id');
        $ap    = DB::table('accounts')->where('code','2000')->value('id');
        $sales = DB::table('accounts')->where('code','4000')->value('id');
        $cogs  = DB::table('accounts')->where('code','5000')->value('id');

        DB::table('transaction_rules')->insert([
            ['source_type'=>'sale','debit_account_id'=>$ar,'credit_account_id'=>$sales],
            ['source_type'=>'purchase','debit_account_id'=>$cogs,'credit_account_id'=>$ap],
        ]);
    }
}
