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
		// Chart of Accounts
		Schema::create('accounts', function (Blueprint $table) {
			$table->id();
			$table->foreignId('company_id')->constrained()->onDelete('cascade');
			$table->string('code')->unique();
			$table->string('name');
			$table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense']);
			$table->enum('sub_type', ['cash', 'receivable', 'payable', 'sales', 'purchase', 'inventory'])->nullable();
			$table->string('currency', 3)->default('USD');
			$table->decimal('balance', 15, 2)->default(0);
			$table->text('description')->nullable();
			$table->boolean('is_active')->default(true);
			$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
			$table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['company_id', 'type']);
			$table->index(['company_id', 'code']);
		});

		// Customers & Suppliers
		Schema::create('parties', function (Blueprint $table) {
			$table->id();
			$table->foreignId('company_id')->constrained()->onDelete('cascade');
			$table->string('name');
			$table->enum('type', ['customer', 'supplier']);
			$table->string('email')->nullable();
			$table->string('phone')->nullable();
			$table->text('address')->nullable();
			$table->string('tax_number')->nullable();
			$table->decimal('balance', 15, 2)->default(0);
			$table->boolean('is_active')->default(true);
			$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
			$table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['company_id', 'type']);
			$table->index(['company_id', 'name']);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
        Schema::dropIfExists('parties');
        Schema::dropIfExists('accounts');
	}
};
