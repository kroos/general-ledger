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

class Party extends Model
{
	use HasFactory, SoftDeletes;
	// protected $connection = '';
	protected $table = 'parties';
	// protected $primaryKey = '';
	// public $incrementing = false;
	// protected $keyType = '';
	// const CREATED_AT = '';
	// const UPDATED_AT = '';
	// protected $rememberTokenName = '';

	protected $fillable = [
		'company_id', 'name', 'type', 'email', 'phone', 'address',
		'tax_number', 'balance', 'is_active', 'created_by'
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

	public function sales()
	{
		return $this->hasMany(SalesLedger::class, 'customer_id');
	}

	public function purchases()
	{
		return $this->hasMany(PurchaseLedger::class, 'supplier_id');
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
		if ($this->type === 'customer') {
			$totalSales = $this->sales()->where('status', '!=', 'cancelled')->sum('total_amount');
			$totalPayments = $this->ledgerEntries()
			->whereHas('account', function ($query) {
				$query->where('sub_type', 'cash');
			})
			->sum('debit');
			$this->balance = $totalSales - $totalPayments;
		} else {
			$totalPurchases = $this->purchases()->where('status', '!=', 'cancelled')->sum('total_amount');
			$totalPayments = $this->ledgerEntries()
			->whereHas('account', function ($query) {
				$query->where('sub_type', 'cash');
			})
			->sum('credit');
			$this->balance = $totalPurchases - $totalPayments;
		}

		$this->save();
	}

	public function getOutstandingInvoices()
	{
		if ($this->type === 'customer') {
			return $this->sales()->where('status', 'pending')->get();
		} else {
			return $this->purchases()->where('status', 'pending')->get();
		}
	}
}
