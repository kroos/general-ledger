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
		// General Ledger (master transactions)
		Schema::create('general_ledgers', function (Blueprint $table) {
			$table->id();
			$table->foreignId('company_id')->constrained()->onDelete('cascade');
			$table->date('transaction_date');
			$table->string('reference_number')->nullable();
			$table->text('description');
			$table->enum('transaction_type', ['sale', 'purchase', 'payment', 'receipt', 'journal', 'adjustment']);
			$table->string('currency', 3)->default('USD');
			$table->decimal('exchange_rate', 15, 6)->default(1);
			$table->decimal('total_debit', 15, 2)->default(0);
			$table->decimal('total_credit', 15, 2)->default(0);
			$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
			$table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
			$table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
			$table->timestamp('approved_at')->nullable();
			$table->timestamps();
			$table->softDeletes();

			$table->index(['company_id', 'transaction_date']);
			$table->index(['company_id', 'transaction_type']);
			$table->index(['company_id', 'created_by']);
		});

		// General Ledger Entries (double-entry system)
		Schema::create('general_ledger_entries', function (Blueprint $table) {
			$table->id();
			$table->foreignId('company_id')->constrained()->onDelete('cascade');
			$table->foreignId('general_ledger_id')->constrained()->onDelete('cascade');
			$table->foreignId('account_id')->constrained();
			$table->foreignId('party_id')->nullable()->constrained();
			$table->decimal('debit', 15, 2)->default(0);
			$table->decimal('credit', 15, 2)->default(0);
			$table->text('notes')->nullable();
			$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
			$table->timestamps();

			$table->index(['company_id', 'account_id']);
			$table->index(['company_id', 'party_id']);
			$table->index(['general_ledger_id', 'account_id']);
		});

		// Sales Ledger (customer transactions)
		Schema::create('sales_ledgers', function (Blueprint $table) {
			$table->id();
			$table->foreignId('company_id')->constrained()->onDelete('cascade');
			$table->foreignId('customer_id')->constrained('parties');
			$table->date('sale_date');
			$table->string('invoice_number')->unique();
			$table->decimal('amount', 15, 2);
			$table->decimal('tax_amount', 15, 2)->default(0);
			$table->decimal('total_amount', 15, 2);
			$table->string('currency', 3)->default('USD');
			$table->enum('status', ['draft', 'pending', 'paid', 'overdue', 'cancelled'])->default('draft');
			$table->date('due_date')->nullable();
			$table->date('payment_date')->nullable();
			$table->text('description')->nullable();
			$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
			$table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
			$table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
			$table->timestamp('approved_at')->nullable();
			$table->timestamps();
			$table->softDeletes();

			$table->index(['company_id', 'customer_id']);
			$table->index(['company_id', 'sale_date']);
			$table->index(['company_id', 'status']);
			$table->index(['company_id', 'created_by']);
		});

		// Purchase Ledger (supplier transactions)
		Schema::create('purchase_ledgers', function (Blueprint $table) {
			$table->id();
			$table->foreignId('company_id')->constrained()->onDelete('cascade');
			$table->foreignId('supplier_id')->constrained('parties');
			$table->date('purchase_date');
			$table->string('invoice_number')->unique();
			$table->decimal('amount', 15, 2);
			$table->decimal('tax_amount', 15, 2)->default(0);
			$table->decimal('total_amount', 15, 2);
			$table->string('currency', 3)->default('USD');
			$table->enum('status', ['draft', 'pending', 'paid', 'overdue', 'cancelled'])->default('draft');
			$table->date('due_date')->nullable();
			$table->date('payment_date')->nullable();
			$table->text('description')->nullable();
			$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
			$table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
			$table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
			$table->timestamp('approved_at')->nullable();
			$table->timestamps();
			$table->softDeletes();

			$table->index(['company_id', 'supplier_id']);
			$table->index(['company_id', 'purchase_date']);
			$table->index(['company_id', 'status']);
			$table->index(['company_id', 'created_by']);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('purchase_ledgers');
		Schema::dropIfExists('sales_ledgers');
		Schema::dropIfExists('general_ledger_entries');
		Schema::dropIfExists('general_ledgers');	}
};
