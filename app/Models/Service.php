<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Fournisseur;

class Service extends Model
{
    use HasFactory;

    protected $primaryKey = 'ser_id';

    public const CREATED_AT = 'ser_created_at';
    public const UPDATED_AT = 'ser_updated_at';

    /**
     * Get data for the services and suppliers management index.
     */
    public static function getIndexData()
    {
        return [
            'services' => self::paginate(3, ['*'], 'services')->withQueryString(),
            'fournisseurs' => Fournisseur::paginate(3, ['*'], 'fournisseurs')->withQueryString(),
        ];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ser_nom',
        'ser_code',
        'ser_type',
        'ser_responsable',
        'ser_etage',
    ];

    /**
     * Get the users belonging to this service.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'ser_id', 'ser_id');
    }

    /**
     * Get the stock movements for the service.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'mvs_ser_id', 'ser_id');
    }

    /**
     * Get the withdraw requests for the service.
     */
    public function withdrawRequests(): HasMany
    {
        return $this->hasMany(DemandeSortie::class, 'dso_ser_id', 'ser_id');
    }

    // -------------------------- Methodes -------------------------
    public static function getUsersCountAttribute($perPage)
    {
        return self::paginate($perPage, ['*'], 'services')->withQueryString();
    }
    
}

