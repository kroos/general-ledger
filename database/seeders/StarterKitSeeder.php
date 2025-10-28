<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Role;

class StarterKitSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		DB::transaction(function () {
			// Get the system admin user
			$admin = DB::table('users')->where('is_system_admin', true)->first();

			if (!$admin) {
				$this->command->error('System admin user not found. Please run SystemAdminSeeder first.');
				return;
			}

			// 1. Create a demo company
			$companyId = DB::table('companies')->insertGetId([
				'name' => 'Demo Company',
				'legal_name' => 'Demo Company LLC',
				'tax_id' => '123-45-6789',
				'currency' => 'USD',
				'timezone' => 'America/New_York',
				'fiscal_year_start' => '01-01',
				'is_active' => true,
				'settings' => json_encode(['theme' => 'light']),
				'created_by' => $admin->id,
				'owned_by' => $admin->id,
				'created_at' => now(),
				'updated_at' => now(),
			]);

			// 2. Create system roles for this company
			$roles = [
				[
					'company_id' => $companyId,
					'name' => 'owner',
					'description' => 'Company owner with full access',
					'permissions' => json_encode(['*']),
					'is_system_role' => true,
					'is_active' => true,
					'created_by' => $admin->id,
					'created_at' => now(),
					'updated_at' => now(),
				],
				[
					'company_id' => $companyId,
					'name' => 'accountant',
					'description' => 'Can manage all financial transactions and reports',
					'permissions' => json_encode(['ledger.*', 'reports.*', 'accounts.*', 'parties.*']),
					'is_system_role' => true,
					'is_active' => true,
					'created_by' => $admin->id,
					'created_at' => now(),
					'updated_at' => now(),
				],
				[
					'company_id' => $companyId,
					'name' => 'manager',
					'description' => 'Can view reports and manage basic operations',
					'permissions' => json_encode(['ledger.view', 'reports.view', 'parties.view']),
					'is_system_role' => true,
					'is_active' => true,
					'created_by' => $admin->id,
					'created_at' => now(),
					'updated_at' => now(),
				],
				[
					'company_id' => $companyId,
					'name' => 'staff',
					'description' => 'Can create basic transactions',
					'permissions' => json_encode(['ledger.create', 'parties.view']),
					'is_system_role' => true,
					'is_active' => true,
					'created_by' => $admin->id,
					'created_at' => now(),
					'updated_at' => now(),
				]
			];

			$ownerRoleId = null;
			foreach ($roles as $role) {
				$roleId = DB::table('roles')->insertGetId($role);
				if ($role['name'] === 'owner') {
					$ownerRoleId = $roleId;
				}
			}

			// 3. Assign admin as owner of this company
			DB::table('company_user')->insert([
				'company_id' => $companyId,
				'user_id' => $admin->id,
				'role_id' => $ownerRoleId,
				'is_active' => true,
				'assigned_by' => $admin->id,
				'created_at' => now(),
				'updated_at' => now(),
			]);

			// 4. Create chart of accounts
			$accounts = [
				// Assets
				['code' => '1001', 'name' => 'Cash', 'type' => 'asset', 'sub_type' => 'cash', 'balance' => 0],
				['code' => '1002', 'name' => 'Accounts Receivable', 'type' => 'asset', 'sub_type' => 'receivable', 'balance' => 0],
				['code' => '1003', 'name' => 'Inventory', 'type' => 'asset', 'sub_type' => 'inventory', 'balance' => 0],
				['code' => '1004', 'name' => 'Office Equipment', 'type' => 'asset', 'sub_type' => null, 'balance' => 0],

				// Liabilities
				['code' => '2001', 'name' => 'Accounts Payable', 'type' => 'liability', 'sub_type' => 'payable', 'balance' => 0],
				['code' => '2002', 'name' => 'Sales Tax Payable', 'type' => 'liability', 'sub_type' => null, 'balance' => 0],
				['code' => '2003', 'name' => 'Loans Payable', 'type' => 'liability', 'sub_type' => null, 'balance' => 0],

				// Equity
				['code' => '3001', 'name' => 'Owner\'s Capital', 'type' => 'equity', 'sub_type' => null, 'balance' => 0],
				['code' => '3002', 'name' => 'Retained Earnings', 'type' => 'equity', 'sub_type' => null, 'balance' => 0],
				['code' => '3003', 'name' => 'Current Earnings', 'type' => 'equity', 'sub_type' => null, 'balance' => 0],

				// Revenue
				['code' => '4001', 'name' => 'Sales Revenue', 'type' => 'revenue', 'sub_type' => 'sales', 'balance' => 0],
				['code' => '4002', 'name' => 'Service Revenue', 'type' => 'revenue', 'sub_type' => null, 'balance' => 0],
				['code' => '4003', 'name' => 'Interest Income', 'type' => 'revenue', 'sub_type' => null, 'balance' => 0],

				// Expenses
				['code' => '5001', 'name' => 'Cost of Goods Sold', 'type' => 'expense', 'sub_type' => 'purchase', 'balance' => 0],
				['code' => '5002', 'name' => 'Rent Expense', 'type' => 'expense', 'sub_type' => null, 'balance' => 0],
				['code' => '5003', 'name' => 'Utilities Expense', 'type' => 'expense', 'sub_type' => null, 'balance' => 0],
				['code' => '5004', 'name' => 'Salaries Expense', 'type' => 'expense', 'sub_type' => null, 'balance' => 0],
				['code' => '5005', 'name' => 'Office Supplies Expense', 'type' => 'expense', 'sub_type' => null, 'balance' => 0],
				['code' => '5006', 'name' => 'Advertising Expense', 'type' => 'expense', 'sub_type' => null, 'balance' => 0],
			];

			foreach ($accounts as $account) {
				DB::table('accounts')->insert(array_merge($account, [
					'company_id' => $companyId,
					'currency' => 'USD',
					'is_active' => true,
					'created_by' => $admin->id,
					'created_at' => now(),
					'updated_at' => now(),
				]));
			}

			// 5. Create sample customer and supplier
			$parties = [
				[
					'company_id' => $companyId,
					'name' => 'John Doe Customer',
					'type' => 'customer',
					'email' => 'john@example.com',
					'phone' => '+1234567890',
					'address' => '123 Customer Street, City, State 12345',
					'tax_number' => 'CUST-001',
					'balance' => 0,
					'is_active' => true,
					'created_by' => $admin->id,
					'created_at' => now(),
					'updated_at' => now(),
				],
				[
					'company_id' => $companyId,
					'name' => 'ABC Supplies Inc.',
					'type' => 'supplier',
					'email' => 'supplier@abc.com',
					'phone' => '+0987654321',
					'address' => '456 Supplier Ave, City, State 54321',
					'tax_number' => 'SUPP-001',
					'balance' => 0,
					'is_active' => true,
					'created_by' => $admin->id,
					'created_at' => now(),
					'updated_at' => now(),
				]
			];

			DB::table('parties')->insert($parties);

			$this->command->info('Starter kit created successfully!');
			$this->command->info('Company: Demo Company');
			$this->command->info('Login: admin / admin123');
		});    }
}
