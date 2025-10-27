<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SystemRole;

class SystemAdminSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		// Create default system roles
		SystemRole::createDefaultRoles();

		// Create initial system admin user
		$admin = User::create([
			'name' => 'System Administrator',
			'email' => 'admin@system.local',
			'timezone' => 'UTC',
			'is_system_admin' => true,
			'is_active' => true,
		]);

		// Create login for system admin
		$admin->logins()->create([
			'username' => 'admin',
			'password' => bcrypt('admin123'), // Change in production!
			'type' => 'username',
			'is_active' => true,
			'created_by' => $admin->id,
		]);

		// Assign system admin role
		$systemAdminRole = SystemRole::where('name', 'system_admin')->first();
		$admin->systemRoles()->attach($systemAdminRole, ['assigned_by' => $admin->id]);

		// Create default system settings
		\App\Models\SystemSetting::create([
			'key' => 'system.maintenance_mode',
			'value' => false,
			'type' => 'boolean',
			'description' => 'Put the entire system in maintenance mode',
			'category' => 'system',
			'is_public' => true,
			'updated_by' => $admin->id,
		]);

		\App\Models\SystemSetting::create([
			'key' => 'system.company.registration_allowed',
			'value' => true,
			'type' => 'boolean',
			'description' => 'Allow new company registrations',
			'category' => 'company',
			'is_public' => true,
			'updated_by' => $admin->id,
		]);    }
	}
