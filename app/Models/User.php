<?php

namespace App\Models;

use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasFactory;
    use HasRoles;
    use SoftDeletes;
    use Notifiable;

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'dob' => 'datetime',
        'deleted_at' => 'timestamp',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'dob',
        'instagram',
        'image',
        'phone',
        'phone_emergency',
        'height',
        'goal_id',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function diaries(): HasMany
    {
        return $this->hasMany(Diary::class);
    }

    public function notes(): belongsToMany
    {
        return $this->belongsToMany(Note::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'author_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    protected function dob(): Attribute
    {
        return Attribute::make(
            get: fn($value, array $attributes) => $value === null
                ? null
                : Carbon::parse($value)->format('d-m-Y'),
        );
    }

    protected function instagramUrl(): Attribute
    {
        return Attribute::make(
            get: fn(
                $value,
                array $attributes
            ) => $attributes['instagram'] === null
                ? null
                : 'https://www.instagram.com/'.$attributes['instagram'],
        );
    }
}
