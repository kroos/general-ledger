<?php
namespace App\Models\Accounting;

// use Illuminate\Database\Eloquent\Model;
use App\Models\Model;
use App\Trait\Auditable;

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

class Journal extends Model
{
	use SoftDeletes, Auditable;

	protected static $auditIncludeSnapshot = true;
	protected static $auditCriticalEvents = ['posted','voided','deleted','force_deleted'];

	// protected $connection = '';
	protected $table = 'journals';
	// protected $primaryKey = '';
	// public $incrementing = false;
	// protected $keyType = '';
	// const CREATED_AT = '';
	// const UPDATED_AT = '';
	// protected $rememberTokenName = '';

	protected $guarded = [];
	protected $dates = ['posted_at', 'deleted_at'];

	// Tell Eloquent not to treat this as a database column
	protected $appends = [];
	protected $attributes = [];
	protected $hidden = [];
	protected $fillable = [
		'date',
		'reference_no',
		'ledger_type_id',
		'source_type',
		'source_id',
		'description',
		'status',
		'posted_at',
	];

	protected array $nonPersistent = ['oldAttributesForAudit'];

	protected $casts = [
		'date' => 'date',
	];

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// set column attribute
	// public function setNameAttribute($value)
	// {
	//     $this->attributes['name'] = ucwords(Str::lower($value));
	// }

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// relationship
	/** Each journal belongs to one ledger type (General, Sales, Purchase, etc.) */
	public function ledgerType()
	{
		return $this->belongsTo(LedgerType::class, 'ledger_type_id');
	}

	/** Each journal has many journal entries (debit/credit lines) */
	public function entries()
	{
		return $this->hasMany(JournalEntry::class, 'journal_id');
	}

		/*
		|--------------------------------------------------------------------------
		| Scopes & Helpers
		|--------------------------------------------------------------------------
		*/

	/** Check if the journal is balanced */
	public function isBalanced(): bool
	{
		$debit = $this->entries->sum('debit');
		$credit = $this->entries->sum('credit');
		return bccomp((string)$debit, (string)$credit, 2) === 0;
	}

	/** Quick helper: status labels */
	public function getStatusLabelAttribute(): string
	{
		return match ($this->status) {
			'draft' => 'Draft',
			'posted' => 'Posted',
			'cancelled' => 'Cancelled',
			default => ucfirst($this->status ?? 'Unknown'),
		};
	}

	/** Cascade soft deletes for entries */
	protected static function booted()
	{
		static::deleting(function ($journal) {
			if ($journal->isForceDeleting()) {
				$journal->entries()->forceDelete();
			} else {
				$journal->entries()->delete();
			}
		});

		static::restoring(function ($journal) {
			$journal->entries()->withTrashed()->restore();
		});
	}
}
