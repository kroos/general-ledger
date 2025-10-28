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

class SystemActivityLog extends Model
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
        'user_id', 'action', 'description', 'context', 'ip_address', 'user_agent', 'performed_at'
    ];

    protected $casts = [
        'context' => 'array',
        'performed_at' => 'datetime',
    ];

    /////////////////////////////////////////////////////////////////////////////////////////////////////
    // set column attribute
    // public function setNameAttribute($value)
    // {
    //     $this->attributes['name'] = ucwords(Str::lower($value));
    // }

    /////////////////////////////////////////////////////////////////////////////////////////////////////
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Methods
    public static function log($action, $description, $context = [], $user = null)
    {
        if (!$user && auth()->check()) {
            $user = auth()->user();
        }

        return self::create([
            'user_id' => $user ? $user->id : null,
            'action' => $action,
            'description' => $description,
            'context' => $context,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'performed_at' => now(),
        ]);
    }

    public function getContextFormatted()
    {
        return json_encode($this->context, JSON_PRETTY_PRINT);
    }
}
