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

class FinancialPeriod extends Model
{
    //
    use HasFactory;
    // protected $connection = '';
    // protected $table = '';
    // protected $primaryKey = '';
    // public $incrementing = false;
    // protected $keyType = '';
    // const CREATED_AT = '';
    // const UPDATED_AT = '';
    // protected $rememberTokenName = '';

    protected $fillable = [
        'company_id', 'name', 'start_date', 'end_date', 'is_closed',
        'closed_at', 'closed_by', 'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'closed_at' => 'datetime',
        'is_closed' => 'boolean',
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

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Methods
    public function close($userId)
    {
        $this->update([
            'is_closed' => true,
            'closed_at' => now(),
            'closed_by' => $userId,
        ]);
    }

    public function isCurrent()
    {
        return !$this->is_closed &&
               $this->start_date <= now() &&
               $this->end_date >= now();
    }

    public function isPast()
    {
        return $this->end_date < now();
    }

    public function isFuture()
    {
        return $this->start_date > now();
    }

    public function getTransactions()
    {
        return $this->company->generalLedgers()
                    ->whereBetween('transaction_date', [$this->start_date, $this->end_date])
                    ->get();
    }
}
