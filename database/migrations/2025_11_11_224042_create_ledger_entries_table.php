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
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->charset('utf8mb4');
            $table->collation('utf8mb4_unicode_ci');
            $table->id();
            $table->datetime('date')->nullable();
            $table->foreignId('ledger_id')->constrained('ledgers')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('ledger_debit_id')->nullable()->constrained('ledgers')->cascadeOnDelete();
            $table->decimal('debit', 15, 2)->default(0);
            $table->text('description_debit')->nullable();
            $table->foreignId('ledger_credit_id')->nullable()->constrained('ledgers')->cascadeOnDelete();
            $table->decimal('credit', 15, 2)->default(0);
            $table->text('description_credit')->nullable();
            $table->string('reference')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
