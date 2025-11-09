<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
	public function run()
	{
		// Roles
		$owner = Role::firstOrCreate(['name' => 'owner']);
		$accountant = Role::firstOrCreate(['name' => 'accountant']);
		$manager = Role::firstOrCreate(['name' => 'manager']);
		$staff = Role::firstOrCreate(['name' => 'staff']);

		// Permissions
		$permissions = [
			'manage accounts',
			'manage journals',
			'manage sales invoices',
			'manage purchase bills',
			'manage payments',
			'view reports',
			'post entries',
			'delete records',
		];

		foreach ($permissions as $perm) {
			Permission::firstOrCreate(['name' => $perm]);
		}

		// Assign permissions
		$owner->givePermissionTo(Permission::all());
		$accountant->givePermissionTo([
			'manage accounts', 'manage journals', 'manage sales invoices',
			'manage purchase bills', 'manage payments', 'view reports', 'post entries'
		]);
		$manager->givePermissionTo(['view reports']);
		$staff->givePermissionTo(['manage sales invoices', 'manage purchase bills', 'manage payments']);
	}
}
