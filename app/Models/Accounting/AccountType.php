<?php
namespace App\Models\Accounting;

// use Illuminate\Database\Eloquent\Model;
use App\Models\Model;

use Illuminate\Database\Eloquent\SoftDeletes;
// use Illuminate\Database\Eloquent\Relations\HasOne;
// use Illuminate\Database\Eloquent\Relations\HasOneThrough;
// use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Relations\HasManyThrough;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// load helper
use Illuminate\Support\Str;

class AccountType extends Model
{
	//
	use SoftDeletes;
	// protected $connection = '';
	protected $table = 'account_types';
	// protected $primaryKey = '';
	// public $incrementing = false;
	// protected $keyType = '';
	// const CREATED_AT = '';
	// const UPDATED_AT = '';
	// protected $rememberTokenName = '';

	// protected $casts = [
	// 	'is_active' => 'boolean',
	// ];

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// set column attribute
	public function setAccountTypeAttribute($value)
	{
		$this->attributes['account_type'] = ucwords(Str::lower($value));
	}

	public function setDescriptionAttribute($value)
	{
		return $this->attributes['description'] = ucwords(Str::lower($value));
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// relationship
	public function hasmanyaccount(): HasMany
	{
		return $this->HasMany(\App\Models\Accounting\Account::class, 'account_type_id');
	}
}
