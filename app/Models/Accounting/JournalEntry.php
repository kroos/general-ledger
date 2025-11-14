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

class JournalEntry extends Model
{
	//
	use SoftDeletes;
	// protected $connection = '';
	protected $table = 'journal_entries';
	// protected $primaryKey = '';
	// public $incrementing = false;
	// protected $keyType = '';
	// const CREATED_AT = '';
	// const UPDATED_AT = '';
	// protected $rememberTokenName = '';

	protected $casts = [
		'debit' => 'decimal:2',
		'credit' => 'decimal:2',
		'date' => 'date',
	];

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// set column attribute
	public function setNoReferenceDebitAttribute($value)
	{
		$this->attributes['no_reference_debit'] = Str::upper(Str::lower($value));
	}

	public function setNoReferenceCreitAttribute($value)
	{
		$this->attributes['no_reference_credit'] = Str::upper(Str::lower($value));
	}

	public function setDescriptionDebitAttribute($value)
	{
		$this->attributes['description_debit'] = ucfirst(Str::lower($value));
	}

	public function setDescriptionCreditAttribute($value)
	{
		$this->attributes['description_credit'] = ucfirst(Str::lower($value));
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// relationship

	public function belongstojournal(): BelongsTo
	{
		return $this->BelongsTo(App\Models\Accounting\Journal::class, 'journal_id');
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
