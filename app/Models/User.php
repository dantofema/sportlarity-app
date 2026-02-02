<?php

namespace App\Models;

use Illuminate\Support\Facades\Date;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $hidden = [
        'password',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'password_change_required',
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
            get: fn ($value, array $attributes): ?string => $value === null
                ? null
                : Date::parse($value)->format('d-m-Y'),
        );
    }

    protected function instagramUrl(): Attribute
    {
        return Attribute::make(
            get: fn (
                $value,
                array $attributes
            ) => $attributes['instagram'] === null
                ? null
                : 'https://www.instagram.com/'.$attributes['instagram'],
        );
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes): ?string => $attributes['image'] === null
                ? null
                : route('secure.avatar', ['filename' => basename($attributes['image'])]),
        );
    }
    protected function casts(): array
    {
        return [
            'dob' => 'datetime',
            'deleted_at' => 'timestamp',
            'password_change_required' => 'boolean',
        ];
    }
}
