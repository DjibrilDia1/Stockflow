<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class MouvementStock extends Model
{
    use HasFactory;

    // =========================================================================
    // CONFIGURATION
    // =========================================================================

    protected $table      = 'mouvements_stock';
    protected $primaryKey = 'mvs_id';

    public const CREATED_AT = 'mvs_created_at';
    public const UPDATED_AT = 'mvs_updated_at';

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

    protected $casts = [
        'mvs_date_mouvement' => 'datetime',
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /** Article concerné par le mouvement. */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'mvs_art_id', 'art_id');
    }

    /** Entrepôt source du mouvement. */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Entrepot::class, 'mvs_ent_id', 'ent_id');
    }

    /** Fournisseur lié au mouvement (entrée). */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class, 'mvs_fou_id', 'fou_id');
    }

    /** Service demandeur (sortie). */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'mvs_ser_id', 'ser_id');
    }

    /** Utilisateur ayant initié le mouvement. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mvs_usr_id', 'id');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /** Charge toutes les relations nécessaires à l'affichage d'un mouvement. */
    public function scopeWithFullDetails($query)
    {
        return $query->with(['item', 'warehouse', 'supplier', 'service', 'user']);
    }

    /** Filtre les mouvements de type OUT uniquement. */
    public function scopeOut($query)
    {
        return $query->where('mvs_type', 'OUT');
    }

    /** Filtre les mouvements du mois en cours. */
    public function scopeThisMonth($query)
    {
        $now = \Carbon\Carbon::now();
        return $query->whereBetween('mvs_date_mouvement', [
            $now->copy()->startOfMonth(),
            $now,
        ]);
    }

    /**
     * Filtre les mouvements selon les critères de recherche du controller.
     * Critères supportés : search, type, article_id, warehouse_id, date_start, date_end.
     */
    public function scopeFiltered($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('mvs_motif', 'like', "%{$filters['search']}%")
                  ->orWhereHas('item', function ($q) use ($filters) {
                      $q->where('art_nom', 'like', "%{$filters['search']}%")
                        ->orWhere('art_reference', 'like', "%{$filters['search']}%");
                  });
            });
        }

        if (!empty($filters['type'])) {
            $query->where('mvs_type', $filters['type']);
        }

        if (!empty($filters['article_id'])) {
            $query->where('mvs_art_id', $filters['article_id']);
        }

        if (!empty($filters['warehouse_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('mvs_ent_id', $filters['warehouse_id'])
                  ->orWhere('mvs_ent_dest_id', $filters['warehouse_id']);
            });
        }

        if (!empty($filters['date_start'])) {
            $query->whereDate('mvs_date_mouvement', '>=', $filters['date_start']);
        }

        if (!empty($filters['date_end'])) {
            $query->whereDate('mvs_date_mouvement', '<=', $filters['date_end']);
        }

        return $query;
    }

    // =========================================================================
    // MÉTHODES STATIQUES — LECTURE / REQUÊTES
    // =========================================================================

    /**
     * Retourne les mouvements paginés et filtrés pour la vue index du gestionnaire.
     */
    public static function getPaginatedMovements(array $filters, int $perPage = 4)
    {
        return self::withFullDetails()
            ->filtered($filters)
            ->orderByDesc('mvs_date_mouvement')
            ->orderByDesc('mvs_created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Retourne les N articles les plus consommés (mouvements OUT).
     * Utilisé dans le dashboard et les rapports.
     */
    public static function topConsumedArticles(int $limit = 5): \Illuminate\Support\Collection
    {
        return static::where('mvs_type', 'OUT')
            ->selectRaw('mvs_art_id, SUM(mvs_quantite) as total_qty')
            ->groupBy('mvs_art_id')
            ->orderByDesc('total_qty')
            ->take($limit)
            ->with('item')
            ->get()
            ->map(fn ($m) => [
                'name'  => $m->item->art_nom ?? 'Inconnu',
                'value' => (int) $m->total_qty,
            ]);
    }

    /**
     * Retourne l'article le plus consommé (mouvement OUT, quantité maximale cumulée).
     */
    public static function mostConsumedArticle(): ?static
    {
        return static::where('mvs_type', 'OUT')
            ->select('mvs_art_id', DB::raw('SUM(ABS(mvs_quantite)) as total_qty'))
            ->groupBy('mvs_art_id')
            ->orderByDesc('total_qty')
            ->first();
    }

    // =========================================================================
    // MÉTHODES STATIQUES — ÉCRITURE / TRANSACTIONS
    // =========================================================================

    /**
     * Enregistre un mouvement et met à jour le stock en transaction.
     * Gère les types IN, OUT, ADJUST et TRANSFER (double mouvement + stock dest.).
     */
    public static function recordWithStockUpdate(array $data): void
    {
        DB::transaction(function () use ($data) {
            $movement = static::create($data);

            // Mise à jour du stock source
            $stockSource = StockArticle::firstOrCreate(
                ['sta_art_id' => $data['mvs_art_id'], 'sta_ent_id' => $data['mvs_ent_id']],
                ['sta_quantite' => 0]
            );

            if (in_array($data['mvs_type'], ['IN', 'ADJUST'])) {
                $stockSource->sta_quantite += $data['mvs_quantite'];
            } elseif (in_array($data['mvs_type'], ['OUT', 'TRANSFER'])) {
                $stockSource->sta_quantite -= $data['mvs_quantite'];
            }

            $stockSource->save();

            // Transfert : mise à jour du stock destination + mouvement IN miroir
            if ($data['mvs_type'] === 'TRANSFER' && isset($data['mvs_ent_dest_id'])) {
                $stockDest = StockArticle::firstOrCreate(
                    ['sta_art_id' => $data['mvs_art_id'], 'sta_ent_id' => $data['mvs_ent_dest_id']],
                    ['sta_quantite' => 0]
                );
                $stockDest->sta_quantite += $data['mvs_quantite'];
                $stockDest->save();

                static::create([
                    'mvs_art_id'         => $data['mvs_art_id'],
                    'mvs_ent_id'         => $data['mvs_ent_dest_id'],
                    'mvs_type'           => 'IN',
                    'mvs_quantite'       => $data['mvs_quantite'],
                    'mvs_motif'          => "Transfert depuis l'entrepôt ID: {$data['mvs_ent_id']}. " . ($data['mvs_motif'] ?? ''),
                    'mvs_date_mouvement' => $data['mvs_date_mouvement'],
                    'mvs_usr_id'         => $data['mvs_usr_id'],
                    'mvs_transfer_id'    => $movement->mvs_id,
                ]);
            }
        });
    }

    // =========================================================================
    // MÉTHODES D'INSTANCE — FORMATAGE
    // =========================================================================

    /**
     * Formate le mouvement pour l'affichage dans une liste (dashboard, rapports).
     */
    public function formatForList(): array
    {
        return [
            'id'       => $this->mvs_id,
            'date'     => $this->mvs_date_mouvement->format('d/m/Y'),
            'article'  => $this->item->art_nom ?? 'N/A',
            'type'     => $this->mvs_type,
            'quantity' => $this->mvs_type === 'OUT' ? -$this->mvs_quantite : $this->mvs_quantite,
            'entrepot' => $this->warehouse->ent_nom ?? 'N/A',
            'service'  => $this->service->ser_nom ?? 'N/A',
        ];
    }
}