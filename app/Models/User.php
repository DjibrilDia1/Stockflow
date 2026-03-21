<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Service;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'ser_id',
    ];

    /**
     * Get data for the user management index.
     */
    public static function getIndexData()
    {
        return [
            'users' => self::withService()->orderByDesc('created_at')->paginate(3),
            'services' => Service::all(['ser_id', 'ser_nom']),
        ];
    }

    /**
     * Scope to eager load the service.
     */
    public function scopeWithService($query)
    {
        return $query->with('service');
    }

    /**
     * Get the service that the user belongs to.
     */
    public function service(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Service::class, 'ser_id', 'ser_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Get the stock movements for the user.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'mvs_usr_id', 'id');
    }

    /**
     * Get the withdraw requests for the user.
     */
    public function withdrawRequests(): HasMany
    {
        return $this->hasMany(DemandeSortie::class, 'dso_demandeur_id', 'id');
    }
}

