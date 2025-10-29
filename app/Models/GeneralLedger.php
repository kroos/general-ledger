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

class GeneralLedger extends Model
{
    //
    use HasFactory, SoftDeletes;
    // protected $connection = '';
    protected $table = 'general_ledgers';
    // protected $primaryKey = '';
    // public $incrementing = false;
    // protected $keyType = '';
    // const CREATED_AT = '';
    // const UPDATED_AT = '';
    // protected $rememberTokenName = '';

    protected $fillable = [
        'company_id', 'transaction_date', 'reference_number', 'description',
        'transaction_type', 'currency', 'exchange_rate', 'total_debit',
        'total_credit', 'created_by', 'updated_by', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'exchange_rate' => 'decimal:6',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'approved_at' => 'datetime',
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

    public function entries()
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

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Methods
    public function isBalanced()
    {
        return $this->total_debit == $this->total_credit;
    }

    public function approve($userId)
    {
        $this->update([
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function isApproved()
    {
        return !is_null($this->approved_at);
    }

    public function recalculateTotals()
    {
        $this->total_debit = $this->entries()->sum('debit');
        $this->total_credit = $this->entries()->sum('credit');
        $this->save();
    }

    public function addEntry($accountId, $debit, $credit, $partyId = null, $notes = null)
    {
        return $this->entries()->create([
            'company_id' => $this->company_id,
            'account_id' => $accountId,
            'party_id' => $partyId,
            'debit' => $debit,
            'credit' => $credit,
            'notes' => $notes,
        ]);
    }
}
