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

class Currency extends Model
{
    //
    use HasFactory;
    // protected $connection = '';
    protected $table = 'currencies';
    // protected $primaryKey = '';
    // public $incrementing = false;
    // protected $keyType = '';
    // const CREATED_AT = '';
    // const UPDATED_AT = '';
    // protected $rememberTokenName = '';

    protected $fillable = [
        'code', 'name', 'symbol', 'exchange_rate', 'rate_date', 'is_active', 'created_by'
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:6',
        'rate_date' => 'date',
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
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Methods
    public function convertAmount($amount, $toCurrency)
    {
        if ($this->code === $toCurrency) {
            return $amount;
        }

        // Convert to base currency first (USD), then to target currency
        $amountInBase = $amount / $this->exchange_rate;

        $targetCurrency = self::where('code', $toCurrency)->first();
        if ($targetCurrency) {
            return $amountInBase * $targetCurrency->exchange_rate;
        }

        return $amount; // Fallback
    }

    public function getFormattedAmount($amount)
    {
        $formatted = number_format($amount, 2);

        switch ($this->symbol) {
            case '$':
                return $this->symbol . $formatted;
            case '€':
                return $formatted . ' ' . $this->symbol;
            case '£':
                return $this->symbol . $formatted;
            default:
                return $formatted . ' ' . $this->code;
        }
    }

    public static function getActiveCurrencies()
    {
        return self::where('is_active', true)->get();
    }
}
