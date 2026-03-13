<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';
    protected $primaryKey = 'art_id';

    public const CREATED_AT = 'art_created_at';
    public const UPDATED_AT = 'art_updated_at';

    /**
     * The attributes that are mass assignable.
     */

    protected $fillable = [
        'art_reference',
        'art_nom',
        'art_unite',
        'art_cat_id',
        'art_seuil_alerte',
        'art_stock_securite',
        'art_prix_defaut',
    ];

    /**
     * Get the total stock quantity across all warehouses.
     */
    public function getTotalStockAttribute(): int
    {
        return $this->itemStocks()->sum('sta_quantite');
    }

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['total_stock'];

    /**
     * Get the category that owns the item.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'art_cat_id', 'cat_id');
    }

    /**
     * Get the item stocks for the item.
     */
    public function itemStocks(): HasMany
    {
        return $this->hasMany(StockArticle::class, 'sta_art_id', 'art_id');
    }

    /**
     * Get the stock movements for the item.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'mvs_art_id', 'art_id');
    }

    /**
     * Get the withdraw request lines for the item.
     */
    public function withdrawRequestLines(): HasMany
    {
        return $this->hasMany(LigneDemandeSortie::class, 'lds_art_id', 'art_id');
    }

    /**
     * Scope: articles dont le stock total est inférieur ou égal au seuil d'alerte.
     */
    public function scopeBelowAlertThreshold($query)
    {
        return $query->whereRaw(
            'COALESCE((select SUM(sta_quantite) from stocks_articles where articles.art_id = stocks_articles.sta_art_id), 0) <= art_seuil_alerte'
        )->withSum('itemStocks as total_stock', 'sta_quantite');
    }

    /**
     * Calcule la valeur totale du stock (somme des quantités × prix par défaut).
     */
    public static function totalStockValue(): float
    {
        return (float) (static::join('stocks_articles', 'articles.art_id', '=', 'stocks_articles.sta_art_id')
            ->selectRaw('SUM(stocks_articles.sta_quantite * articles.art_prix_defaut) as total_value')
            ->value('total_value') ?? 0);
    }

    /**
     * Retourne la liste formatée des articles pour la vue demandeur
     */
    public static function forDemandeurDisplay(): \Illuminate\Support\Collection
    {
        return static::with(['category', 'itemStocks.warehouse'])->get()->map(function ($article) {
            return [
                'id'       => $article->art_id,
                'code'     => $article->art_reference,
                'name'     => $article->art_nom,
                'category' => $article->category->cat_nom ?? 'N/A',
                'stock'    => $article->total_stock,
                'status'   => $article->total_stock > $article->art_seuil_alerte
                    ? 'Disponible'
                    : ($article->total_stock > 0 ? 'Stock bas' : 'Rupture'),
                'warehouses' => $article->itemStocks->map(fn ($stock) => [
                    'id'   => $stock->sta_ent_id,
                    'name' => $stock->warehouse->ent_nom ?? 'N/A',
                    'qty'  => $stock->sta_quantite,
                ]),
            ];
        });
    }

    /**
     * Retourne la liste simplifiée des articles disponibles pour le formulaire
     * de demande de sortie (id, nom, entrepôts avec quantités).
     */
    public static function asAvailableList(): \Illuminate\Support\Collection
    {
        return static::with(['itemStocks.warehouse'])->get()->map(function ($article) {
            return [
                'id'         => $article->art_id,
                'nom'        => $article->art_nom,
                'warehouses' => $article->itemStocks->map(fn ($stock) => [
                    'id'   => $stock->sta_ent_id,
                    'name' => $stock->warehouse->ent_nom ?? 'N/A',
                    'qty'  => $stock->sta_quantite,
                ])->values(),
            ];
        });
    }

    // Articles paginer avec les relations
    public static function getWithRelationsPaginated($perPage)
    {
        return self::with(['category', 'itemStocks.warehouse'])->paginate($perPage, ['*'], 'items');
    }


}
