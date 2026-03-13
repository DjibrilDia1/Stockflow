<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stock';
    protected $primaryKey = 'mvs_id';

    public const CREATED_AT = 'mvs_created_at';
    public const UPDATED_AT = 'mvs_updated_at';

    /**
     * The attributes that are mass assignable.
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

    /**
     * Scope: mouvements de type OUT.
     */
    public function scopeOut($query)
    {
        return $query->where('mvs_type', 'OUT');
    }

    /**
     * Scope: mouvements du mois en cours.
     */
    public function scopeThisMonth($query)
    {
        $now = \Carbon\Carbon::now();
        return $query->whereBetween('mvs_date_mouvement', [$now->copy()->startOfMonth(), $now]);
    }

    /**
     * Scope: filtre les mouvements selon les critères du rapport (article, entrepôt, type, dates).
     */
    public function scopeFiltered($query, array $filters)
    {
        if (!empty($filters['article'])) {
            $query->where('mvs_art_id', $filters['article']);
        }
        if (!empty($filters['entrepot'])) {
            $query->where('mvs_ent_id', $filters['entrepot']);
        }
        if (!empty($filters['type'])) {
            $query->where('mvs_type', $filters['type']);
        }
        if (!empty($filters['date_start'])) {
            $query->whereDate('mvs_date_mouvement', '>=', $filters['date_start']);
        }
        if (!empty($filters['date_end'])) {
            $query->whereDate('mvs_date_mouvement', '<=', $filters['date_end']);
        }
        return $query;
    }

    /**
     * Retourne les N articles les plus consommés (mouvements OUT).
     */
    public static function topConsumedArticles(int $limit = 5): \Illuminate\Support\Collection
    {
        return static::where('mvs_type', 'OUT')
            ->selectRaw('mvs_art_id, sum(mvs_quantite) as total_qty')
            ->groupBy('mvs_art_id')
            ->orderByDesc('total_qty')
            ->take($limit)
            ->with('item')
            ->get()
            ->map(function ($m) {
                return [
                    'name'  => $m->item->art_nom ?? 'Inconnu',
                    'value' => (int) $m->total_qty,
                ];
            });
    }

    /**
     * Retourne le mouvement OUT avec la plus grande quantité totale (article le plus consommé).
     */
    public static function mostConsumedArticle(): ?static
    {
        return static::where('mvs_type', 'OUT')
            ->select('mvs_art_id', DB::raw('SUM(ABS(mvs_quantite)) as total_qty'))
            ->groupBy('mvs_art_id')
            ->orderByDesc('total_qty')
            ->first();
    }

    /**
     * Enregistre un mouvement de stock et met à jour les quantités dans un transaction.
     * Gère aussi les transferts (mouvement OUT source + mouvement IN destination).
     */
    public static function recordWithStockUpdate(array $data): void
    {
        DB::transaction(function () use ($data) {
            $movement = static::create($data);

            $stockSource = StockArticle::firstOrCreate(
                ['sta_art_id' => $data['mvs_art_id'], 'sta_ent_id' => $data['mvs_ent_id']],
                ['sta_quantite' => 0]
            );

            if ($data['mvs_type'] === 'IN' || $data['mvs_type'] === 'ADJUST') {
                $stockSource->sta_quantite += $data['mvs_quantite'];
            } elseif ($data['mvs_type'] === 'OUT' || $data['mvs_type'] === 'TRANSFER') {
                $stockSource->sta_quantite -= $data['mvs_quantite'];
            }
            $stockSource->save();

            if ($data['mvs_type'] === 'TRANSFER' && isset($data['mvs_ent_dest_id'])) {
                $stockDest = StockArticle::firstOrCreate(
                    ['sta_art_id' => $data['mvs_art_id'], 'sta_ent_id' => $data['mvs_ent_dest_id']],
                    ['sta_quantite' => 0]
                );
                $stockDest->sta_quantite += $data['mvs_quantite'];
                $stockDest->save();

                static::create([
                    'mvs_art_id'          => $data['mvs_art_id'],
                    'mvs_ent_id'          => $data['mvs_ent_dest_id'],
                    'mvs_type'            => 'IN',
                    'mvs_quantite'        => $data['mvs_quantite'],
                    'mvs_motif'           => "Transfert depuis l'entrepôt ID: " . $data['mvs_ent_id'] . ". " . ($data['mvs_motif'] ?? ''),
                    'mvs_date_mouvement'  => $data['mvs_date_mouvement'],
                    'mvs_usr_id'          => $data['mvs_usr_id'],
                    'mvs_transfer_id'     => $movement->mvs_id,
                ]);
            }
        });
    }
}

