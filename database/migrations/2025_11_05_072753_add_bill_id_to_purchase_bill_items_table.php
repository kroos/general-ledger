<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::table('purchase_bill_items', function (Blueprint $table) {
		 $table->string('bill_id')->nullable()->after('account_id');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table('purchase_bill_items', function (Blueprint $table) {
			$table->dropColumn('bill_id');
		});
	}
};
