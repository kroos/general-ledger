<?php
namespace App\Models\Accounting;

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

class LedgerEntry extends Model
{
	//
	use SoftDeletes;
	// protected $connection = '';
	protected $table = 'ledger_entries';
	// protected $primaryKey = '';
	// public $incrementing = false;
	// protected $keyType = '';
	// const CREATED_AT = '';
	// const UPDATED_AT = '';
	// protected $rememberTokenName = '';

	protected $casts = [
		'debit' => 'decimal:2',
		'credit' => 'decimal:2',
		'date' => 'datetime',
	];

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// set column attribute
	public function setReferenceAttribute($value)
	{
		$this->attributes['reference'] = Str::upper(Str::lower($value));
	}

	public function setDescriptionDebitAttribute($value)
	{
		$this->attributes['description_debit'] = ucwords(Str::lower($value));
	}

	public function setDescriptionCreditAttribute($value)
	{
		$this->attributes['description_credit'] = ucwords(Str::lower($value));
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// relationship
	public function belongstoledger(): BelongsTo
	{
		return $this->BelongsTo(App\Models\Accounting\Ledger::class, 'ledger_id');
	}

	public function belongstoaccount(): BelongsTo
	{
		return $this->BelongsTo(App\Models\Accounting\Account::class, 'account_id');
	}

	public function belongstoledgerdebit(): BelongsTo
	{
		return $this->BelongsTo(App\Models\Accounting\Ledger::class, 'ledger_debit_id');
	}

	public function belongstoledgercredit(): BelongsTo
	{
		return $this->BelongsTo(App\Models\Accounting\Ledger::class, 'ledger_credit_id');
	}

}
