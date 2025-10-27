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
		// Financial periods for reporting
		Schema::create('financial_periods', function (Blueprint $table) {
			$table->id();
			$table->foreignId('company_id')->constrained()->onDelete('cascade');
			$table->string('name'); // Q1 2024, January 2024, etc.
			$table->date('start_date');
			$table->date('end_date');
			$table->boolean('is_closed')->default(false);
			$table->timestamp('closed_at')->nullable();
			$table->foreignId('closed_by')->nullable()->constrained('users');
			$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
			$table->timestamps();

			$table->index(['company_id', 'start_date', 'end_date']);
		});

		// Currencies for multi-currency support
		Schema::create('currencies', function (Blueprint $table) {
			$table->id();
			$table->string('code', 3)->unique(); // USD, EUR, GBP
			$table->string('name');
			$table->string('symbol')->nullable();
			$table->decimal('exchange_rate', 15, 6);
			$table->date('rate_date');
			$table->boolean('is_active')->default(true);
			$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
			$table->timestamps();
		});

		// Document attachments for paperless system
		Schema::create('document_attachments', function (Blueprint $table) {
			$table->id();
			$table->foreignId('company_id')->constrained()->onDelete('cascade');
			$table->string('attachable_type'); // SalesLedger, PurchaseLedger, etc.
			$table->unsignedBigInteger('attachable_id');
			$table->string('filename');
			$table->string('original_name');
			$table->string('mime_type');
			$table->bigInteger('size');
			$table->string('path');
			$table->text('description')->nullable();
			$table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
			$table->timestamps();

			$table->index(['company_id', 'attachable_type', 'attachable_id']);
			$table->index(['company_id', 'uploaded_by']);
		});

		// Notes & comments system
		Schema::create('notes', function (Blueprint $table) {
			$table->id();
			$table->foreignId('company_id')->constrained()->onDelete('cascade');
			$table->string('noteable_type'); // Any model
			$table->unsignedBigInteger('noteable_id');
			$table->text('content');
			$table->boolean('is_private')->default(false);
			$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['company_id', 'noteable_type', 'noteable_id']);
			$table->index(['company_id', 'created_by']);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('notes');
		Schema::dropIfExists('document_attachments');
		Schema::dropIfExists('currencies');
		Schema::dropIfExists('financial_periods');    }
	};
