<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stock';
    protected $primaryKey = 'mvs_id';

    public const CREATED_AT = 'mvs_created_at';
    public const UPDATED_AT = 'mvs_updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'mvs_art_id',
        'mvs_ent_id',
        'mvs_type',
        'mvs_quantite',
        'mvs_motif',
        'mvs_fou_id',
        'mvs_ser_id',
        'mvs_usr_id',
        'mvs_transfer_id',
        'mvs_piece_jointe_url',
        'mvs_date_mouvement',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mvs_date_mouvement' => 'datetime',
    ];

    /**
     * Get the item that was moved.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'mvs_art_id', 'art_id');
    }

    /**
     * Get the warehouse for the movement.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Entrepot::class, 'mvs_ent_id', 'ent_id');
    }

    /**
     * Get the supplier for the movement.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class, 'mvs_fou_id', 'fou_id');
    }

    /**
     * Get the service for the movement.
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'mvs_ser_id', 'ser_id');
    }

    /**
     * Get the user who initiated the movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mvs_usr_id', 'id');
    }
}

