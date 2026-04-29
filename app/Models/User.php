<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Roles válidos del sistema (FIX 62.B) */
    public const ROLES = ['admin', 'coordinador', 'lider', 'comercial', 'cliente'];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /** Validación de rol a nivel modelo (FIX 62.B) */
    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->role && !in_array($user->role, self::ROLES, true)) {
                throw new \InvalidArgumentException("Rol inválido: {$user->role}");
            }
        });
    }

    // --- Relationships ---

    public function comercial(): HasOne
    {
        return $this->hasOne(Comercial::class);
    }

    public function coordinador(): HasOne
    {
        return $this->hasOne(Coordinador::class);
    }

    public function lider(): HasOne
    {
        return $this->hasOne(Lider::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // --- Role helpers ---

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCoordinador(): bool
    {
        return $this->role === 'coordinador';
    }

    public function isLider(): bool
    {
        return $this->role === 'lider';
    }

    public function isComercial(): bool
    {
        return $this->role === 'comercial';
    }

    public function isCliente(): bool
    {
        return $this->role === 'cliente';
    }

    public function dashboardRoute(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'coordinador' => 'coordinador.dashboard',
            'lider' => 'lider.dashboard',
            'comercial' => 'comercial.dashboard',
            default => 'cliente.dashboard',
        };
    }
}
