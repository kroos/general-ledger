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

class SystemRole extends Model
{
	//
	use SoftDeletes;
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
	// protected $fillable = [
	// 	'name', 'description', 'permissions', 'is_active'
	// ];

	protected $guarded = [];

	protected $casts = [
		'permissions' => 'array',
	];

	public function users()
	{
		return $this->belongsToMany(User::class, 'system_role_user')
		->withTimestamps();
	}

	// Pre-defined system roles
	public static function createDefaultRoles()
	{
		$roles = [
			[
				'name' => 'system_admin',
				'description' => 'Full system access across all companies',
				'permissions' => ['*'], // All permissions
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
			self::create($role);
		}
	}

}
