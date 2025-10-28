<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


// db relation class to load
use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Database\Eloquent\Relations\HasOne;
// use Illuminate\Database\Eloquent\Relations\HasOneThrough;
// use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
	/** @use HasFactory<\Database\Factories\UserFactory> */
	use HasFactory, Notifiable, SoftDeletes;

	// protected $connection = 'mysql';
	protected $table = 'users';
	protected $dates = ['deleted_at'];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	// protected $fillable = [
	// 	'name',
	// 	'email',
	// 	'password',
	// ];

	protected $guarded = [];

	/**
	* The attributes that should be cast.
	*
	* @var array<string, string>
	*/
	protected $casts = [
		'email_verified_at' => 'datetime',
		'is_active' => 'boolean',
		'is_system_admin' => 'boolean',
	];

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	public function setNameAttribute($value)
	{
		$this->attributes['name'] = ucwords(Str::lower($value));
	}

	public function setEmailAttribute($value)
	{
		$this->attributes['email'] = Str::lower($value);
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// db relation hasMany/hasOne
	public function logins(): HasMany
	{
		return $this->hasMany(Login::class, 'user_id');
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// db relation belongsToMany
	public function companies(): BelongsToMany
	{
		return $this->belongsToMany(Company::class, 'company_user')
		->withPivot('role_id', 'is_active', 'assigned_by')
		->withTimestamps();
	}

	public function systemRoles(): BelongsToMany
	{
		return $this->belongsToMany(SystemRole::class, 'system_role_user')
		->withTimestamps();
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// belongsto
	public function createdBy(): BelongsTo
	{
		return $this->belongsTo(User::class, 'created_by');
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// acl
	// Methods
	public function isSystemAdmin()
	{
		return $this->is_system_admin || $this->systemRoles()
		->where('name', 'system_admin')
		->exists();
	}

	public function accessibleCompanies()
	{
		if ($this->isSystemAdmin()) {
			return Company::all();
		}

		return $this->companies()->where('is_active', true)->get();
	}

	public function hasSystemPermission($permission)
	{
		if ($this->isSystemAdmin()) {
			return true;
		}

		return $this->systemRoles()
		->where('is_active', true)
		->get()
		->contains(function ($role) use ($permission) {
			return in_array($permission, $role->permissions) ||
			in_array('*', $role->permissions);
		});
	}

	public function getPrimaryLoginAttribute()
	{
		return $this->logins()->where('is_active', true)->first();
	}
}
