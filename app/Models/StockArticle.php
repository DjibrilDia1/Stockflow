<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockArticle extends Model
{
    use HasFactory;

    protected $table = 'stocks_articles';
    protected $primaryKey = 'sta_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'sta_art_id',
        'sta_ent_id',
        'sta_quantite',
    ];

    /**
     * Scope to eager load item and warehouse.
     */
    public function scopeWithDetails($query)
    {
        return $query->with(['item', 'warehouse']);
    }

    /**
     * Get the item that owns the stock.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'sta_art_id', 'art_id');
    }

    /**
     * Get the warehouse that owns the stock.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Entrepot::class, 'sta_ent_id', 'ent_id');
    }

    /**
     * Scope: stocks dont la quantité est inférieure au seuil d'alerte (mais > 0).
     */
    public function scopeLowStock($query)
    {
        return $query->join('articles', 'stocks_articles.sta_art_id', '=', 'articles.art_id')
            ->whereColumn('sta_quantite', '<', 'art_seuil_alerte')
            ->where('sta_quantite', '>', 0);
    }

    /**
     * Scope: stocks à zéro (rupture).
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('sta_quantite', '<=', 0);
    }

    /**
     * Scope: stocks au-dessus du seuil d'alerte (disponible).
     */
    public function scopeAvailable($query)
    {
        return $query->join('articles', 'stocks_articles.sta_art_id', '=', 'articles.art_id')
            ->whereColumn('sta_quantite', '>=', 'art_seuil_alerte');
    }

    /**
     * Retourne le rapport des stocks en alerte ou en critique (pour les rapports).
     */
    public static function lowStockReport(): \Illuminate\Support\Collection
    {
        return static::join('articles', 'stocks_articles.sta_art_id', '=', 'articles.art_id')
            ->join('entrepots', 'stocks_articles.sta_ent_id', '=', 'entrepots.ent_id')
            ->where(function ($query) {
                $query->whereColumn('sta_quantite', '<', 'art_seuil_alerte')
                      ->orWhereColumn('sta_quantite', '<', 'art_stock_securite');
            })
            ->select('stocks_articles.*', 'articles.art_reference', 'articles.art_nom', 'articles.art_seuil_alerte', 'articles.art_stock_securite', 'entrepots.ent_nom')
            ->get()
            ->map(function ($stock) {
                return [
                    'id'             => $stock->sta_id,
                    'reference'      => $stock->art_reference,
                    'nom'            => $stock->art_nom,
                    'entrepot'       => $stock->ent_nom,
                    'stock_actuel'   => $stock->sta_quantite,
                    'seuil_bas'      => $stock->art_seuil_alerte,
                    'stock_securite' => $stock->art_stock_securite,
                    'statut'         => $stock->sta_quantite < $stock->art_stock_securite ? 'Critique' : 'Alerte',
                ];
            });
    }

    /**
     * Trouve le stock d'un article dans un entrepôt donné.
     */
    public static function findStock(int $articleId, int $warehouseId): ?static
    {
        return static::where('sta_art_id', $articleId)
            ->where('sta_ent_id', $warehouseId)
            ->first();
    }
}

