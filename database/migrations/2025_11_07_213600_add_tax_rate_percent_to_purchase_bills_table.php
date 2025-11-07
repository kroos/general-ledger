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
        Schema::table('purchase_bills', function (Blueprint $table) {
            $table->decimal('tax_rate_percent', 15, 2)->default(0)->before('tax');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_bills', function (Blueprint $table) {
            $table->dropColumn('tax_rate_percent');
        });
    }
};
