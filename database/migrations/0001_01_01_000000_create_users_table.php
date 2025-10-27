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
		// Users table - Add system admin flag
		Schema::create('users', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('email')->unique();
			$table->string('phone')->nullable();
			$table->string('timezone')->default('UTC');
			$table->boolean('is_active')->default(true);
			$table->boolean('is_system_admin')->default(false); // ← NEW: System admin flag
			$table->timestamp('email_verified_at')->nullable();
			$table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
			$table->timestamps();
			$table->softDeletes();

			$table->index(['is_system_admin', 'is_active']); // For quick admin queries
		});

		// System-wide roles (not company-specific)
		Schema::create('system_roles', function (Blueprint $table) {
			$table->id();
			$table->string('name'); // system_admin, support_agent, auditor
			$table->string('description')->nullable();
			$table->json('permissions'); // System-wide permissions
			$table->boolean('is_active')->default(true);
			$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
			$table->timestamps();
		});

		// System role assignments
		Schema::create('system_role_user', function (Blueprint $table) {
			$table->id();
			$table->foreignId('system_role_id')->constrained()->onDelete('cascade');
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
			$table->timestamps();

			$table->unique(['system_role_id', 'user_id']);
		});

		// System settings table (global settings)
		Schema::create('system_settings', function (Blueprint $table) {
			$table->id();
			$table->string('key')->unique();
			$table->json('value')->nullable();
			$table->string('type')->default('string'); // string, boolean, integer, json
			$table->text('description')->nullable();
			$table->string('category')->default('general');
			$table->boolean('is_public')->default(false); // Can companies see this?
			$table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
			$table->timestamps();
		});

        // System activity logs (across all companies)
		Schema::create('system_activity_logs', function (Blueprint $table) {
			$table->id();
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->string('action'); // user.created, company.deleted, system.backup
			$table->text('description');
			$table->json('context')->nullable(); // Additional data
			$table->string('ip_address')->nullable();
			$table->string('user_agent')->nullable();
			$table->timestamp('performed_at');

			$table->index(['user_id', 'performed_at']);
			$table->index(['action', 'performed_at']);
		});

		// Companies table (multi-tenant support)
		Schema::create('companies', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->string('legal_name')->nullable();
			$table->string('tax_id')->nullable();
			$table->string('currency', 3)->default('USD');
			$table->string('timezone')->default('UTC');
			$table->string('fiscal_year_start', 5)->default('01-01');
			$table->boolean('is_active')->default(true);
			$table->json('settings')->nullable();
			$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
			$table->foreignId('owned_by')->constrained('users')->onDelete('cascade'); // Primary owner
			$table->timestamps();
			$table->softDeletes();
					});

		// Roles table
		Schema::create('roles', function (Blueprint $table) {
			$table->id();
			$table->foreignId('company_id')->constrained()->onDelete('cascade');
			$table->string('name');
			$table->string('description')->nullable();
			$table->json('permissions');
			$table->boolean('is_system_role')->default(false);
			$table->boolean('is_active')->default(true);
			$table->foreignId('created_by')->constrained('users')->onDelete('cascade');
			$table->timestamps();
		});

		// Company-User relationship (pivot table)
		Schema::create('company_user', function (Blueprint $table) {
			$table->id();
			$table->foreignId('company_id')->constrained()->onDelete('cascade');
			$table->foreignId('user_id')->constrained()->onDelete('cascade');
			$table->foreignId('role_id')->constrained()->onDelete('cascade');
			$table->boolean('is_active')->default(true);
			$table->foreignId('assigned_by')->constrained('users')->onDelete('cascade');
			$table->timestamps();
			$table->unique(['company_id', 'user_id']);
		});

		Schema::create('password_reset_tokens', function (Blueprint $table) {
			$table->string('email')->primary();
			$table->string('token');
			$table->timestamp('created_at')->nullable();
		});

		Schema::create('sessions', function (Blueprint $table) {
			$table->string('id')->primary();
			$table->foreignId('user_id')->nullable()->index();
			$table->string('ip_address', 45)->nullable();
			$table->text('user_agent')->nullable();
			$table->longText('payload');
			$table->integer('last_activity')->index();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('password_reset_tokens');
		Schema::dropIfExists('sessions');
		Schema::dropIfExists('system_activity_logs');
		Schema::dropIfExists('system_settings');
		Schema::dropIfExists('system_role_user');
		Schema::dropIfExists('system_roles');
		Schema::dropIfExists('companies');
		Schema::dropIfExists('users');
	}
};
