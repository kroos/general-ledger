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
		Schema::create('journals', function (Blueprint $table) {
			$table->id();
			$table->date('date')->index();
			$table->string('reference_no')->nullable()->index();
			$table->foreignId('ledger_type_id')->constrained('ledger_types')->cascadeOnDelete();
			$table->string('source_type')->nullable();
			$table->unsignedBigInteger('source_id')->nullable();
			$table->text('description')->nullable();
			$table->enum('status', ['draft','posted','void'])->default('draft');
			$table->softDeletes();
			$table->timestamps();
			$table->index(['source_type','source_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('journals');
	}
};
