<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Categorie;
use App\Models\Entrepot;
use App\Models\MouvementStock;
use Illuminate\Support\Facades\DB;

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
     * Calculate the total value of all stock using SQL for performance.
     */
    public static function getTotalStockValue(): float
    {
        return DB::table('articles')
            ->join('stocks_articles', 'articles.art_id', '=', 'stocks_articles.sta_art_id')
            ->selectRaw('SUM(stocks_articles.sta_quantite * COALESCE(articles.art_prix_defaut, 0)) as total_value')
            ->value('total_value') ?? 0.0;
    }

    /**
     * Get articles that are under their alert threshold using SQL optimization.
     */
    public static function getUnderThreshold()
    {
        return self::withStockDetails()
            ->withSum('itemStocks as calculated_total_stock', 'sta_quantite')
            ->get()
            ->filter(function($article) {
                return ($article->calculated_total_stock ?? 0) <= $article->art_seuil_alerte;
            });
    }

    /**
     * Get all data for the manager dashboard.
     */
    public static function getDashboardData()
    {
        $underThreshold = self::getUnderThreshold();
        return [
            'stats' => [
                'underThreshold' => $underThreshold->count(),
                'totalArticles' => self::count(),
                'movements' => MouvementStock::count(),
                'stockValue' => self::getTotalStockValue(),
            ],
            'stockAlerts' => $underThreshold->take(5)->map(fn($article) => [
                'id' => $article->art_id,
                'product' => $article->art_nom,
                'location' => 'Tous entrepôts',
                'current' => $article->total_stock,
                'threshold' => $article->art_seuil_alerte,
            ])->values(),
            'recentMovements' => MouvementStock::withFullDetails()
                ->latest()
                ->take(5)
                ->get()
                ->map->formatForList(),
            'topArticles' => MouvementStock::getTopConsumed(5)->map(fn($m) => [
                'name' => $m->item->art_nom ?? 'N/A',
                'value' => $m->total_qty
            ]),
        ];
    }

    /**
     * Get paginated articles with all details for the manager.
     */
    public static function getPaginatedForManager($perPage = 3)
    {
        return self::withStockDetails()->paginate($perPage, ['*'], 'items');
    }

    /**
     * Get all data needed for the manager's article index.
     */
    public static function getIndexData()
    {
        return [
            'items' => self::getPaginatedForManager(),
            'categories_all' => Categorie::all(['cat_id', 'cat_nom']),
            'categories' => Categorie::paginate(3, ['*'], 'categories'),
            'warehouses' => Entrepot::all(['ent_id', 'ent_nom']),
        ];
    }

    /**
     * Get data for the demandeur view.
     */
    public static function getForDemandeur()
    {
        $articles = self::withStockDetails()->get();
        return [
            'articles' => $articles->map->formatForDemandeur(),
            'categories' => Categorie::all(['cat_id', 'cat_nom']),
            'articlesDisponibles' => $articles->map->formatAvailability(),
        ];
    }

    /**
     * Create a new article safely.
     */
    public static function storeFromRequest(array $data)
    {
        return self::create($data);
    }

    /**
     * Update article safely.
     */
    public function updateFromRequest(array $data)
    {
        return $this->update($data);
    }

    /**
     * Delete article safely with error handling.
     */
    public function safeDelete()
    {
        return $this->delete();
    }

    /**
     * Format the article for the demandeur list.
     */
    public function formatForDemandeur(): array
    {
        return [
            'id' => $this->art_id,
            'code' => $this->art_reference,
            'name' => $this->art_nom,
            'category' => $this->category->cat_nom ?? 'N/A',
            'stock' => $this->total_stock,
            'status' => $this->status,
            'warehouses' => $this->itemStocks->map(fn($stock) => [
                'id' => $stock->sta_ent_id,
                'name' => $stock->warehouse->ent_nom ?? 'N/A',
                'qty' => $stock->sta_quantite,
            ]),
        ];
    }

    /**
     * Format the article for the availability list (simple view).
     */
    public function formatAvailability(): array
    {
        return [
            'id' => $this->art_id,
            'nom' => $this->art_nom,
            'warehouses' => $this->itemStocks->map(fn($stock) => [
                'id' => $stock->sta_ent_id,
                'name' => $stock->warehouse->ent_nom ?? 'N/A',
                'qty' => $stock->sta_quantite,
            ])->values()
        ];
    }

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
    protected $appends = ['total_stock', 'status'];

    /**
     * Scope to eager load category and stock details.
     */
    public function scopeWithStockDetails($query)
    {
        return $query->with(['category', 'itemStocks.warehouse']);
    }

    /**
     * Get the stock status label.
     */
    public function getStatusAttribute(): string
    {
        $total = $this->total_stock;
        if ($total > $this->art_seuil_alerte) {
            return 'Disponible';
        }
        return $total > 0 ? 'Stock bas' : 'Rupture';
    }

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

    /**
     * Check if enough stock exists in a specific warehouse.
     */
    public function hasStock(int $warehouseId, int $quantity): bool
    {
        $stock = $this->itemStocks()->where('sta_ent_id', $warehouseId)->first();
        return $stock && $stock->sta_quantite >= $quantity;
    }
}
