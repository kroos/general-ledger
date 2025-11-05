<?php
namespace App\Models\Accounting;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

class JournalEntry extends Model
{
	use HasFactory, SoftDeletes, Auditable;

	protected static $auditIncludeSnapshot = true;
	protected static $auditCriticalEvents = ['posted','voided','deleted','force_deleted'];

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
	];

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// set column attribute
	// public function setNameAttribute($value)
	// {
	//     $this->attributes['name'] = ucwords(Str::lower($value));
	// }

	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// relationship
	public function journal()
	{
		return $this->belongsTo(Journal::class, 'journal_id');
	}

	public function account()
	{
		return $this->belongsTo(Account::class, 'account_id');
	}
}
