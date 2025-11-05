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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['receive', 'make'])->index(); // receive = from customer, make = to supplier
            $table->date('date');
            $table->string('reference_no')->nullable();
            $table->decimal('amount', 15, 2);
            $table->foreignId('account_id')->nullable()->index(); // bank or cash account
            $table->morphs('source'); // links to SalesInvoice or PurchaseBill
            $table->string('status')->default('posted'); // posted only for now
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
