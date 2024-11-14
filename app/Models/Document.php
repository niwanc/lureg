<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'file_path', 'status','uuid'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function signatureRequests(): HasMany
    {
        return $this->hasMany(SignatureRequest::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(Signature::class);
    }
}
