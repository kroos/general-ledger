<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use App\Models\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Database\Eloquent\Relations\HasOne;
// use Illuminate\Database\Eloquent\Relations\HasOneThrough;
// use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
// use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Relations\HasManyThrough;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// load helper
use Illuminate\Support\Str;

class Role extends Model
{
	use HasFactory;
	// protected $connection = '';
	// protected $table = '';
	// protected $primaryKey = '';
	// public $incrementing = false;
	// protected $keyType = '';
	// const CREATED_AT = '';
	// const UPDATED_AT = '';
	// protected $rememberTokenName = '';


	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// set column attribute
	// public function setNameAttribute($value)
	// {
	//     $this->attributes['name'] = ucwords(Str::lower($value));
	// }

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// relationship
	protected $fillable = [
		'company_id', 'name', 'description', 'permissions',
		'is_system_role', 'is_active', 'created_by'
	];

	protected $casts = [
		'permissions' => 'array',
		'is_system_role' => 'boolean',
		'is_active' => 'boolean',
	];

	// Relationships
	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function users()
	{
		return $this->belongsToMany(User::class, 'company_user')
		->withTimestamps();
	}

	public function createdBy()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	// Methods
	public function hasPermission($permission)
	{
		return in_array($permission, $this->permissions) ||
		in_array('*', $this->permissions);
	}

	public static function createSystemRoles($companyId, $createdBy)
	{
		$roles = [
			[
				'name' => 'owner',
				'description' => 'Company owner with full access',
				'permissions' => ['*'],
				'is_system_role' => true,
			],
			[
				'name' => 'accountant',
				'description' => 'Can manage all financial transactions and reports',
				'permissions' => ['ledger.*', 'reports.*', 'accounts.*', 'parties.*'],
				'is_system_role' => true,
			],
			[
				'name' => 'manager',
				'description' => 'Can view reports and manage basic operations',
				'permissions' => ['ledger.view', 'reports.view', 'parties.view'],
				'is_system_role' => true,
			],
			[
				'name' => 'staff',
				'description' => 'Can create basic transactions',
				'permissions' => ['ledger.create', 'parties.view'],
				'is_system_role' => true,
			]
		];

		foreach ($roles as $role) {
			self::create(array_merge($role, [
				'company_id' => $companyId,
				'created_by' => $createdBy,
			]));
		}
	}
}
