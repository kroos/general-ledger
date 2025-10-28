<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\SystemRole;
use App\Models\SystemSetting;

class SystemAdminSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// Use DB facade to bypass model constraints for initial setup
		DB::transaction(function () {
			// 1. FIRST create the system admin user WITHOUT created_by
			$adminId = DB::table('users')->insertGetId([
				'name' => 'System Administrator',
				'email' => 'admin@system.local',
				'phone' => null,
				'timezone' => 'UTC',
				'is_active' => true,
				'is_system_admin' => true,
                'created_by' => null, // Self-created, so null is okay
                'created_at' => now(),
                'updated_at' => now(),
              ]);

			// 2. Create login for system admin
			DB::table('logins')->insert([
				'user_id' => $adminId,
				'username' => 'admin',
                'password' => Hash::make('admin123'), // Change this in production!
                'type' => 'username',
                'is_active' => true,
                'created_by' => $adminId, // Now we have admin ID
                'created_at' => now(),
                'updated_at' => now(),
              ]);

			// 3. Create system roles WITH created_by
			$systemRoles = [
				[
					'name' => 'system_admin',
					'description' => 'Full system access across all companies',
					'permissions' => json_encode(['*']),
					'is_active' => true,
					'created_by' => $adminId,
					'created_at' => now(),
					'updated_at' => now(),
				],
				[
					'name' => 'support_agent',
					'description' => 'Can view all companies for support purposes',
					'permissions' => json_encode([
						'companies.view_all',
						'users.view_all',
						'system.logs.view',
						'system.reports.global'
					]),
					'is_active' => true,
					'created_by' => $adminId,
					'created_at' => now(),
					'updated_at' => now(),
				],
				[
					'name' => 'auditor',
					'description' => 'Can view all data for audit purposes',
					'permissions' => json_encode([
						'companies.view_all',
						'data.export_all',
						'system.reports.global'
					]),
					'is_active' => true,
					'created_by' => $adminId,
					'created_at' => now(),
					'updated_at' => now(),
				]
			];

			foreach ($systemRoles as $role) {
				$roleId = DB::table('system_roles')->insertGetId($role);

                // Assign system_admin role to our admin user
				if ($role['name'] === 'system_admin') {
					DB::table('system_role_user')->insert([
						'system_role_id' => $roleId,
						'user_id' => $adminId,
						'assigned_by' => $adminId,
						'created_at' => now(),
						'updated_at' => now(),
					]);
				}
			}

			// 4. Create system settings
			$systemSettings = [
				[
					'key' => 'system.maintenance_mode',
					'value' => json_encode(false),
					'type' => 'boolean',
					'description' => 'Put the entire system in maintenance mode',
					'category' => 'system',
					'is_public' => true,
					'updated_by' => $adminId,
					'created_at' => now(),
					'updated_at' => now(),
				],
				[
					'key' => 'system.company.registration_allowed',
					'value' => json_encode(true),
					'type' => 'boolean',
					'description' => 'Allow new company registrations',
					'category' => 'company',
					'is_public' => true,
					'updated_by' => $adminId,
					'created_at' => now(),
					'updated_at' => now(),
				],
				[
					'key' => 'system.default_currency',
					'value' => json_encode('USD'),
					'type' => 'string',
					'description' => 'Default system currency',
					'category' => 'system',
					'is_public' => true,
					'updated_by' => $adminId,
					'created_at' => now(),
					'updated_at' => now(),
				]
			];

			DB::table('system_settings')->insert($systemSettings);

			// 5. Create default currencies
			$currencies = [
				[
					'code' => 'USD',
					'name' => 'US Dollar',
					'symbol' => '$',
					'exchange_rate' => 1.000000,
					'rate_date' => now()->format('Y-m-d'),
					'is_active' => true,
					'created_by' => $adminId,
					'created_at' => now(),
					'updated_at' => now(),
				],
				[
					'code' => 'EUR',
					'name' => 'Euro',
					'symbol' => '€',
					'exchange_rate' => 0.850000,
					'rate_date' => now()->format('Y-m-d'),
					'is_active' => true,
					'created_by' => $adminId,
					'created_at' => now(),
					'updated_at' => now(),
				],
				[
					'code' => 'GBP',
					'name' => 'British Pound',
					'symbol' => '£',
					'exchange_rate' => 0.730000,
					'rate_date' => now()->format('Y-m-d'),
					'is_active' => true,
					'created_by' => $adminId,
					'created_at' => now(),
					'updated_at' => now(),
				]
			];

			DB::table('currencies')->insert($currencies);
		});
	}
}
