<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class DemandeSortie extends Model
{
    use HasFactory;

    // =========================================================================
    // CONFIGURATION
    // =========================================================================

    protected $table      = 'demandes_sortie';
    protected $primaryKey = 'dso_id';

    public const CREATED_AT = 'dso_created_at';
    public const UPDATED_AT = 'dso_updated_at';

    /** Statuts possibles — utiliser ces constantes plutôt que des strings brutes. */
    public const STATUS_DRAFT     = 'DRAFT';
    public const STATUS_APPROVED  = 'APPROVED';
    public const STATUS_FULFILLED = 'FULFILLED';
    public const STATUS_REJECTED  = 'REJECTED';

    protected $fillable = [
        'dso_ser_id',
        'dso_demandeur_id',
        'dso_statut',
    ];

    /**
     * $appends retire — ces accesseurs déclenchent lines->sum() à chaque
     * sérialisation du modèle, même quand la relation n'est pas chargée.
     * Appeler ->reference, ->status_label, ->total_quantity explicitement
     * dans formatForUser() suffit.
     */

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /** Service ayant émis la demande. */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'dso_ser_id', 'ser_id');
    }

    /** Utilisateur ayant créé la demande. */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dso_demandeur_id', 'id');
    }

    /** Lignes de la demande (articles + quantités demandées). */
    public function lines(): HasMany
    {
        return $this->hasMany(LigneDemandeSortie::class, 'lds_dso_id', 'dso_id');
    }

    // =========================================================================
    // ACCESSEURS
    // =========================================================================

    /** Référence formatée : DSO-00001 */
    public function getReferenceAttribute(): string
    {
        return 'DSO-' . str_pad($this->dso_id, 5, '0', STR_PAD_LEFT);
    }

    /** Libellé lisible du statut. */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->dso_statut) {
            self::STATUS_DRAFT     => 'En validation',
            self::STATUS_APPROVED  => 'En préparation',
            self::STATUS_FULFILLED => 'Prête',
            self::STATUS_REJECTED  => 'Rejetée',
            default                => $this->dso_statut,
        };
    }

    /**
     * Quantité totale demandée sur toutes les lignes.
     * Nécessite la relation lines chargée en amont.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->lines->sum('lds_qte_demandee');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /** Charge toutes les relations nécessaires à l'affichage d'une demande. */
    public function scopeWithDetails($query)
    {
        return $query->with(['service', 'requester', 'lines.item', 'lines.warehouse']);
    }

    /** Filtre les demandes d'un utilisateur donné. */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('dso_demandeur_id', $userId);
    }

    /** Demandes en cours : DRAFT ou APPROVED. */
    public function scopeInProgress($query)
    {
        return $query->whereIn('dso_statut', [self::STATUS_DRAFT, self::STATUS_APPROVED]);
    }

    /** Demandes traitées : FULFILLED. */
    public function scopeProcessed($query)
    {
        return $query->where('dso_statut', self::STATUS_FULFILLED);
    }

    /** Demandes rejetées : REJECTED. */
    public function scopeRejected($query)
    {
        return $query->where('dso_statut', self::STATUS_REJECTED);
    }

    // =========================================================================
    // MÉTHODES STATIQUES — LECTURE / REQUÊTES
    // =========================================================================

    /**
     * Statistiques des demandes pour un utilisateur donné.
     * Utilise une seule requête avec selectRaw pour éviter 3 COUNT séparés.
     */
    public static function getStatsForUser(int $userId): array
    {
        $counts = self::where('dso_demandeur_id', $userId)
            ->selectRaw("
                SUM(CASE WHEN dso_statut IN ('" . self::STATUS_DRAFT . "', '" . self::STATUS_APPROVED . "') THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN dso_statut = '" . self::STATUS_FULFILLED . "' THEN 1 ELSE 0 END) as processed,
                SUM(CASE WHEN dso_statut = '" . self::STATUS_REJECTED . "' THEN 1 ELSE 0 END) as rejected
            ")
            ->first();

        return [
            'inProgress' => (int) ($counts->in_progress ?? 0),
            'processed'  => (int) ($counts->processed   ?? 0),
            'rejected'   => (int) ($counts->rejected     ?? 0),
        ];
    }

    /**
     * Demandes paginées pour la vue index du gestionnaire.
     */
    public static function getManagerIndexData(): array
    {
        return [
            'withdrawRequests' => self::withDetails()
                ->latest()
                ->paginate(3),
        ];
    }

    // =========================================================================
    // MÉTHODES STATIQUES — ÉCRITURE
    // =========================================================================

    /**
     * Crée une demande avec sa première ligne en une seule opération.
     */
    public static function createWithLine(int $serviceId, int $demandeurId, array $lineData): static
    {
        $demande = static::create([
            'dso_ser_id'       => $serviceId,
            'dso_demandeur_id' => $demandeurId,
            'dso_statut'       => self::STATUS_DRAFT,
        ]);

        $demande->lines()->create([
            'lds_art_id'       => $lineData['article_id'],
            'lds_ent_id'       => $lineData['entrepot_id'],
            'lds_qte_demandee' => $lineData['quantite'],
            'lds_note'         => $lineData['motif'] ?? null,
        ]);

        return $demande;
    }

    // =========================================================================
    // MÉTHODES D'INSTANCE — LOGIQUE MÉTIER
    // =========================================================================

    /**
     * Approuve la demande : vérifie le stock, le déduit et crée les mouvements OUT.
     * Tout est enveloppé dans une transaction pour garantir la cohérence des données.
     * Retourne false si la demande n'est pas en DRAFT ou si le stock est insuffisant.
     */
    public function approve(int $gestionnaireId): bool
    {
        if ($this->dso_statut !== self::STATUS_DRAFT) {
            return false;
        }

        return DB::transaction(function () use ($gestionnaireId) {
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

            $this->update(['dso_statut' => self::STATUS_APPROVED]);
            return true;
        });
    }

    /** Rejette la demande. */
    public function reject(): bool
    {
        return $this->update(['dso_statut' => self::STATUS_REJECTED]);
    }

    // =========================================================================
    // MÉTHODES D'INSTANCE — FORMATAGE
    // =========================================================================

    /**
     * Formate la demande pour l'affichage dans la liste du demandeur.
     * Nécessite withDetails() chargé en amont.
     */
    public function formatForUser(): array
    {
        return [
            'id'      => $this->dso_id,
            'ref'     => $this->reference,
            'type'    => 'Retrait',
            'article' => $this->lines->first()?->item?->art_nom ?? 'Multiples',
            'qty'     => $this->total_quantity,
            'date'    => $this->dso_created_at->format('d/m/Y'),
            'status'  => $this->status_label,
        ];
    }
}