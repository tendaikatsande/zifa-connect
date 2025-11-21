<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
        'avatar_url',
        'meta',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'meta' => 'array',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles')
            ->withPivot('assigned_at');
    }

    public function permissions(): BelongsToMany
    {
        return $this->morphToMany(Permission::class, 'model', 'model_has_permissions');
    }

    public function clubs(): BelongsToMany
    {
        return $this->belongsToMany(Club::class, 'club_officials')
            ->withPivot('position', 'status', 'start_date', 'end_date')
            ->withTimestamps();
    }

    public function player(): HasOne
    {
        return $this->hasOne(Player::class);
    }

    public function official(): HasOne
    {
        return $this->hasOne(Official::class);
    }

    public function referee(): HasOne
    {
        return $this->hasOne(Referee::class);
    }

    public function agent(): HasOne
    {
        return $this->hasOne(Agent::class);
    }

    public function createdClubs(): HasMany
    {
        return $this->hasMany(Club::class, 'created_by');
    }

    public function createdPlayers(): HasMany
    {
        return $this->hasMany(Player::class, 'created_by');
    }

    public function initiatedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'initiated_by');
    }

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists()
            || $this->roles()->whereHas('permissions', fn($q) => $q->where('name', $permission))->exists();
    }
}
