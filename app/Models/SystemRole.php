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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// load helper
use Illuminate\Support\Str;

class SystemRole extends Model
{
	//
	use HasFactory;
	// protected $connection = '';
	// protected $table = '';
	// protected $primaryKey = '';
	// public $incrementing = false;
	// protected $keyType = '';
	// const CREATED_AT = '';
	// const UPDATED_AT = '';
	// protected $rememberTokenName = '';

	protected $fillable = [
		'name', 'description', 'permissions', 'is_active', 'created_by'
	];

	protected $casts = [
		'permissions' => 'array',
		'is_active' => 'boolean',
	];

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// set column attribute
	// public function setNameAttribute($value)
	// {
	//     $this->attributes['name'] = ucwords(Str::lower($value));
	// }

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// Relationships
	public function users(): BelongsToMany
	{
		return $this->belongsToMany(User::class, 'system_role_user')
		->withTimestamps();
	}

	public function createdBy(): BelongsTo
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// Methods
	public function hasPermission($permission)
	{
		return in_array($permission, $this->permissions) ||
		in_array('*', $this->permissions);
	}

	public static function createDefaultRoles($createdBy)
	{
		$roles = [
			[
				'name' => 'system_admin',
				'description' => 'Full system access across all companies',
				'permissions' => ['*'],
			],
			[
				'name' => 'support_agent',
				'description' => 'Can view all companies for support purposes',
				'permissions' => [
					'companies.view_all',
					'users.view_all',
					'system.logs.view',
					'system.reports.global'
				],
			],
			[
				'name' => 'auditor',
				'description' => 'Can view all data for audit purposes',
				'permissions' => [
					'companies.view_all',
					'data.export_all',
					'system.reports.global'
				],
			]
		];

		foreach ($roles as $role) {
			self::create(array_merge($role, [
				'is_active' => true,
				'created_by' => $createdBy,
			]));
		}
	}

}
