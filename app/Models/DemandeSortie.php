<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\StockArticle;
use App\Models\MouvementStock;
use App\Models\Article;

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
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['reference', 'status_label', 'total_quantity'];

    /**
     * Get the formatted reference.
     */
    public function getReferenceAttribute(): string
    {
        return 'DSO-' . str_pad($this->dso_id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the human-readable status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->dso_statut) {
            'DRAFT' => 'En validation',
            'APPROVED' => 'En préparation',
            'FULFILLED' => 'Prête',
            'REJECTED' => 'Rejetée',
            default => $this->dso_statut
        };
    }

    /**
     * Get the total quantity across all lines.
     */
    public function getTotalQuantityAttribute(): int
    {
        return $this->lines->sum('lds_qte_demandee');
    }

    /**
     * Scope to eager load details.
     */
    public function scopeWithDetails($query)
    {
        return $query->with(['service', 'requester', 'lines.item', 'lines.warehouse']);
    }

    /**
     * Scope to filter by requester.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('dso_demandeur_id', $userId);
    }


    /**
     * Reject the request.
     */
    public function reject(): bool
    {
        return $this->update(['dso_statut' => 'REJECTED']);
    }

    /**
     * Get statistics for a specific requester.
     */
    public static function getStatsForUser($userId): array
    {
        return [
            'inProgress' => self::where('dso_demandeur_id', $userId)
                ->whereIn('dso_statut', ['DRAFT', 'APPROVED'])
                ->count(),
            'processed' => self::where('dso_demandeur_id', $userId)
                ->where('dso_statut', 'FULFILLED')
                ->count(),
            'rejected' => self::where('dso_demandeur_id', $userId)
                ->where('dso_statut', 'REJECTED')
                ->count(),
        ];
    }

    /**
     * Get data for the manager's request index.
     */
    public static function getManagerIndexData()
    {
        return [
            'withdrawRequests' => self::withDetails()
                ->latest()
                ->paginate(3),
        ];
    }

    /**
     * Get data for the demandeur's request index.
     */
    public static function getDemandeurIndexData($userId)
    {
        return [
            'requests' => self::forUser($userId)
                ->withDetails()
                ->latest()
                ->get()
                ->map->formatForUser(),
            'articlesDisponibles' => Article::withStockDetails()
                ->get()
                ->map->formatAvailability(),
        ];
    }

    /**
     * Format the request for the user list.
     */
    public function formatForUser(): array
    {
        return [
            'id' => $this->dso_id,
            'ref' => $this->reference,
            'type' => 'Retrait',
            'article' => $this->lines->first()->item->art_nom ?? 'Multiples',
            'qty' => $this->total_quantity,
            'date' => $this->dso_created_at->format('d/m/Y'),
            'status' => $this->status_label,
        ];
    }


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
        if ($this->dso_statut !== 'DRAFT') {
            return false;
        }

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

        $this->update(['dso_statut' => 'APPROVED']);
        return true;
    }
}

