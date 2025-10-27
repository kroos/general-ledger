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
		// Logins table (multiple logins per user)
		Schema::create('logins', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->string('username')->unique();
			$table->string('password');
			$table->enum('type', ['email', 'username', 'phone'])->default('email');
			$table->boolean('is_active')->default(true);
			$table->timestamp('last_login_at')->nullable();
			$table->string('last_login_ip')->nullable();
			$table->rememberToken();
			$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('logins');
	}
};
