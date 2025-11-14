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

class Journal extends Model
{
	//
	use SoftDeletes;
	// protected $connection = '';
	protected $table = 'journals';
	// protected $primaryKey = '';
	// public $incrementing = false;
	// protected $keyType = '';
	// const CREATED_AT = '';
	// const UPDATED_AT = '';
	// protected $rememberTokenName = '';

	protected $casts = [
		'date' => 'date',
	];

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// set column attribute
	public function setNoReferenceAttribute($value)
	{
		$this->attributes['no_reference'] = Str::upper(Str::lower($value));
	}

	public function setDescriptionAttribute($value)
	{
		$this->attributes['description'] = ucfirst(Str::lower($value));
	}

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// relationship
	public function belongstoledger(): BelongsTo
	{
		return $this->BelongsTo(\App\Models\Accounting\Ledger::class, 'ledger_id');
	}

	public function hasmanyjournalentries(): HasMany
	{
		return $this->HasMany(\App\Models\Accounting\JournalEntry::class, 'journal_id');
	}

}
