<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Diary extends Model
{
    use HasFactory;

    protected $casts = [
        'id' => 'integer',
        'date' => 'datetime',
        'user_id' => 'integer',
    ];

    protected $fillable = [
        'date',
        'content',
        'user_id',
    ];

    public function isOwner(): bool
    {
        return $this->id !== auth()->user()->id;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(Assessment::class);
    }
}
