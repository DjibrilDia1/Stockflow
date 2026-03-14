<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneDemandeSortie extends Model
{
    use HasFactory;

    protected $table = 'lignes_demande_sortie';
    protected $primaryKey = 'lds_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'lds_dso_id',
        'lds_art_id',
        'lds_ent_id',
        'lds_qte_demandee',
        'lds_qte_servie',
        'lds_note',
    ];

    /**
     * Scope to eager load related models.
     */
    public function scopeWithDetails($query)
    {
        return $query->with(['withdrawRequest', 'item', 'warehouse']);
    }

    /**
     * Get the withdraw request that owns the line.
     */
    public function withdrawRequest(): BelongsTo
    {
        return $this->belongsTo(DemandeSortie::class, 'lds_dso_id', 'dso_id');
    }

    /**
     * Get the item for the line.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'lds_art_id', 'art_id');
    }

    /**
     * Get the warehouse for the line.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Entrepot::class, 'lds_ent_id', 'ent_id');
    }

    // --------------------------- Methodes ---------------------------
    
}

