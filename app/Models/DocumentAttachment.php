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

class DocumentAttachment extends Model
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
        'company_id', 'attachable_type', 'attachable_id', 'filename',
        'original_name', 'mime_type', 'size', 'path', 'description', 'uploaded_by'
    ];

    protected $casts = [
        'size' => 'integer',
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

    public function attachable()
    {
        return $this->morphTo();
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Methods
    public function getFileSizeFormatted()
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    public function isImage()
    {
        return strpos($this->mime_type, 'image/') === 0;
    }

    public function isPdf()
    {
        return $this->mime_type === 'application/pdf';
    }

    public function getDownloadUrl()
    {
        return route('documents.download', $this->id);
    }
}
