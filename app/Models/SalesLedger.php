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

class SalesLedger extends Model
{
    //
    use HasFactory, SoftDeletes;
    // protected $connection = '';
    protected $table = 'sales_ledgers';
    // protected $primaryKey = '';
    // public $incrementing = false;
    // protected $keyType = '';
    // const CREATED_AT = '';
    // const UPDATED_AT = '';
    // protected $rememberTokenName = '';

    protected $fillable = [
        'company_id', 'customer_id', 'sale_date', 'invoice_number',
        'amount', 'tax_amount', 'total_amount', 'currency', 'status',
        'due_date', 'payment_date', 'description', 'created_by',
        'updated_by', 'approved_by', 'approved_at'
    ];

    protected $casts = [
        'sale_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
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

    public function customer()
    {
        return $this->belongsTo(Party::class, 'customer_id');
    }

    public function attachments()
    {
        return $this->morphMany(DocumentAttachment::class, 'attachable');
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
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
    public function markAsPaid($paymentDate = null)
    {
        $this->update([
            'status' => 'paid',
            'payment_date' => $paymentDate ?? now(),
        ]);
    }

    public function isOverdue()
    {
        return $this->status === 'pending' &&
               $this->due_date &&
               $this->due_date->isPast();
    }

    public function calculateTotal()
    {
        $this->total_amount = $this->amount + $this->tax_amount;
        return $this->total_amount;
    }

    public function getBalanceDue()
    {
        if ($this->status === 'paid') {
            return 0;
        }
        return $this->total_amount;
    }

    // Auto-update status based on due date
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($sale) {
            if ($sale->isOverdue()) {
                $sale->status = 'overdue';
            }
        });
    }
}
