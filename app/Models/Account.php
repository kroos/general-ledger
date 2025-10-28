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

class Account extends Model
{
	//
	use HasFactory, SoftDeletes;
	// protected $connection = '';
	// protected $table = '';
	// protected $primaryKey = '';
	// public $incrementing = false;
	// protected $keyType = '';
	// const CREATED_AT = '';
	// const UPDATED_AT = '';
	// protected $rememberTokenName = '';

	protected $fillable = [
		'company_id', 'code', 'name', 'type', 'sub_type', 'currency',
		'balance', 'description', 'is_active', 'created_by'
	];

	protected $casts = [
		'balance' => 'decimal:2',
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
	public function company()
	{
		return $this->belongsTo(Company::class);
	}

	public function ledgerEntries()
	{
		return $this->hasMany(GeneralLedgerEntry::class);
	}

	public function createdBy()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	public function updatedBy()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

    // Methods
	public function updateBalance()
	{
		$debitTotal = $this->ledgerEntries()->sum('debit');
		$creditTotal = $this->ledgerEntries()->sum('credit');

		if (in_array($this->type, ['asset', 'expense'])) {
			$this->balance = $debitTotal - $creditTotal;
		} else {
			$this->balance = $creditTotal - $debitTotal;
		}

		$this->save();
	}

	public function getBalanceAttribute($value)
	{
		return (float) $value;
	}

	public function isDebitAccount()
	{
		return in_array($this->type, ['asset', 'expense']);
	}

	public function isCreditAccount()
	{
		return in_array($this->type, ['liability', 'equity', 'revenue']);
	}
}
