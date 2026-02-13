<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DemandeSortie extends Model
{
    use HasFactory;

    protected $table = 'demandes_sortie';
    protected $primaryKey = 'dso_id';

    public const CREATED_AT = 'dso_created_at';
    public const UPDATED_AT = 'dso_updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dso_ser_id',
        'dso_demandeur_id',
        'dso_statut',
    ];

    /**
     * Get the service that requested the withdrawal.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'dso_ser_id', 'ser_id');
    }

    /**
     * Get the user who requested the withdrawal.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dso_demandeur_id', 'id');
    }

    /**
     * Get the lines for the withdraw request.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(LigneDemandeSortie::class, 'lds_dso_id', 'dso_id');
    }
}

