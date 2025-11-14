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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// load helper
use Illuminate\Support\Str;

class Ledger extends Model
{
	//
	use SoftDeletes;
	// protected $connection = '';
	protected $table = 'ledgers';
	// protected $primaryKey = '';
	// public $incrementing = false;
	// protected $keyType = '';
	// const CREATED_AT = '';
	// const UPDATED_AT = '';
	// protected $rememberTokenName = '';

	protected $casts = [
		'date' => 'datetime',
	];

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// set column attribute
	public function setLedgerAttribute($value)
	{
		$this->attributes['ledger'] = ucwords(Str::lower($value));
	}

	public function setDescriptionAttribute($value)
	{
		$this->attributes['description'] = ucwords(Str::lower($value));
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// relationship
	public function belongstoaccounttype(): BelongsTo
	{
		return $this->BelongsTo(\App\Models\Accounting\AccountType::class, 'account_type_id');
	}

	public function hasmanyjournal(): HasMany
	{
		return $this->HasMany(\App\Models\Accounting\Journal::class, 'ledger_id');
	}


}
