<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_name',
        'file_path',
        'file_hash',
        'file_size',
        'mime_type',
        'width',
        'height',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
