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

class SystemSetting extends Model
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
        'key', 'value', 'type', 'description', 'category', 'is_public', 'updated_by'
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /////////////////////////////////////////////////////////////////////////////////////////////////////
    // set column attribute
    // public function setNameAttribute($value)
    // {
    //     $this->attributes['name'] = ucwords(Str::lower($value));
    // }

    /////////////////////////////////////////////////////////////////////////////////////////////////////
    // Relationships
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Methods
    public function getValueAttribute($value)
    {
        switch ($this->type) {
            case 'boolean':
                return (bool) json_decode($value);
            case 'integer':
                return (int) json_decode($value);
            case 'array':
            case 'json':
                return json_decode($value, true);
            default:
                return json_decode($value);
        }
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = json_encode($value);
    }

    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setValue($key, $value, $type = 'string', $description = null, $updatedBy = null)
    {
        $setting = self::where('key', $key)->first();

        if (!$setting) {
            $setting = new self();
            $setting->key = $key;
            $setting->type = $type;
            $setting->description = $description;
            $setting->category = 'general';
        }

        $setting->value = $value;
        $setting->updated_by = $updatedBy;
        $setting->save();

        return $setting;
    }
}
