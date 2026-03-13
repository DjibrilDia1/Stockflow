<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\StockArticle;
use App\Models\MouvementStock;

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

    

    /**
     * Scope: demandes appartenant à un utilisateur donné.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('dso_demandeur_id', $userId);
    }

    /**
     * Scope: demandes en cours (DRAFT ou APPROVED).
     */
    public function scopeInProgress($query)
    {
        return $query->whereIn('dso_statut', ['DRAFT', 'APPROVED']);
    }

    /**
     * Scope: demandes traitées (FULFILLED).
     */
    public function scopeProcessed($query)
    {
        return $query->where('dso_statut', 'FULFILLED');
    }

    /**
     * Scope: demandes rejetées (REJECTED).
     */
    public function scopeRejected($query)
    {
        return $query->where('dso_statut', 'REJECTED');
    }

    /**
     * Crée une demande de sortie avec sa première ligne.
     */
    public static function createWithLine(int $serviceId, int $demandeurId, array $lineData): static
    {
        $demande = static::create([
            'dso_ser_id'       => $serviceId,
            'dso_demandeur_id' => $demandeurId,
            'dso_statut'       => 'DRAFT',
        ]);

        $demande->lines()->create([
            'lds_art_id'       => $lineData['article_id'],
            'lds_ent_id'       => $lineData['entrepot_id'],
            'lds_qte_demandee' => $lineData['quantite'],
            'lds_note'         => $lineData['motif'] ?? null,
        ]);

        return $demande;
    }

    /**
     * Approuve la demande : déduit le stock et crée les mouvements OUT.
     * Retourne false si le stock est insuffisant pour une ligne.
     */
    public function approve(int $gestionnaireId): bool
    {
        foreach ($this->lines as $line) {
            $stock = StockArticle::findStock($line->lds_art_id, $line->lds_ent_id);

            if (!$stock || $stock->sta_quantite < $line->lds_qte_demandee) {
                return false;
            }

            $stock->decrement('sta_quantite', $line->lds_qte_demandee);

            MouvementStock::create([
                'mvs_art_id'         => $line->lds_art_id,
                'mvs_ent_id'         => $line->lds_ent_id,
                'mvs_ser_id'         => $this->dso_ser_id,
                'mvs_usr_id'         => $gestionnaireId,
                'mvs_quantite'       => $line->lds_qte_demandee,
                'mvs_type'           => 'OUT',
                'mvs_date_mouvement' => now(),
            ]);
        }

        return true;
    }
}

