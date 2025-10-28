<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use App\Models\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Database\Eloquent\Relations\HasOne;
// use Illuminate\Database\Eloquent\Relations\HasOneThrough;
// use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// load helper
use Illuminate\Support\Str;

class Company extends Model
{
	//
	use SoftDeletes, HasFactory;
	// protected $connection = '';
	// protected $table = '';
	// protected $primaryKey = '';
	// public $incrementing = false;
	// protected $keyType = '';
	// const CREATED_AT = '';
	// const UPDATED_AT = '';
	// protected $rememberTokenName = '';

	protected $casts = [
		'settings' => 'array',
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
		return $this->belongsToMany(User::class, 'company_user')
		->withPivot('role_id', 'is_active', 'assigned_by')
		->withTimestamps();
	}

	public function roles(): HasMany
	{
		return $this->hasMany(Role::class);
	}

	public function accounts(): HasMany
	{
		return $this->hasMany(Account::class);
	}

	public function parties(): HasMany
	{
		return $this->hasMany(Party::class);
	}

	public function generalLedgers(): HasMany
	{
		return $this->hasMany(GeneralLedger::class);
	}

	public function salesLedgers(): HasMany
	{
		return $this->hasMany(SalesLedger::class);
	}

	public function purchaseLedgers(): HasMany
	{
		return $this->hasMany(PurchaseLedger::class);
	}

	public function financialPeriods(): HasMany
	{
		return $this->hasMany(FinancialPeriod::class);
	}

	public function createdBy(): BelongsTo
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function ownedBy(): BelongsTo
	{
		return $this->belongsTo(User::class, 'owned_by');
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// Methods
	public function getDefaultAccounts()
	{
		return [
			'cash' => $this->accounts()->where('sub_type', 'cash')->first(),
			'receivable' => $this->accounts()->where('sub_type', 'receivable')->first(),
			'payable' => $this->accounts()->where('sub_type', 'payable')->first(),
			'sales' => $this->accounts()->where('sub_type', 'sales')->first(),
			'purchase' => $this->accounts()->where('sub_type', 'purchase')->first(),
		];
	}

	public function getCurrentFinancialPeriod()
	{
		return $this->financialPeriods()
		->where('is_closed', false)
		->where('start_date', '<=', now())
		->where('end_date', '>=', now())
		->first();
	}
}
