<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Article extends Model
{
    use HasFactory;

    // =========================================================================
    // CONFIGURATION
    // =========================================================================

    protected $table      = 'articles';
    protected $primaryKey = 'art_id';

    public const CREATED_AT = 'art_created_at';
    public const UPDATED_AT = 'art_updated_at';

    protected $fillable = [
        'art_reference',
        'art_nom',
        'art_unite',
        'art_cat_id',
        'art_seuil_alerte',
        'art_stock_securite',
        'art_prix_defaut',
    ];

    // =========================================================================
    // RELATIONS
    // =========================================================================

    /** Catégorie de l'article. */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'art_cat_id', 'cat_id');
    }

    /** Stocks de l'article par entrepôt. */
    public function itemStocks(): HasMany
    {
        return $this->hasMany(StockArticle::class, 'sta_art_id', 'art_id');
    }

    /** Mouvements de stock liés à l'article. */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'mvs_art_id', 'art_id');
    }

    /** Lignes de demandes de sortie liées à l'article. */
    public function withdrawRequestLines(): HasMany
    {
        return $this->hasMany(LigneDemandeSortie::class, 'lds_art_id', 'art_id');
    }

    // =========================================================================
    // ACCESSEURS
    // =========================================================================

    /**
     * Stock total cumulé sur tous les entrepôts.
     * Utilise calculated_total_stock (withSum) en priorité pour éviter
     * toute requête SQL individuelle.
     */
    public function getTotalStockAttribute(): int
    {
        if (isset($this->attributes['calculated_total_stock'])) {
            return (int) $this->attributes['calculated_total_stock'];
        }
        if ($this->relationLoaded('itemStocks')) {
            return (int) $this->itemStocks->sum('sta_quantite');
        }
        return 0;
    }

    /**
     * Statut lisible du stock : Disponible, Stock bas ou Rupture.
     * Dépend de getTotalStockAttribute — ne pas appeler sans withStockDetails().
     */
    public function getStatusAttribute(): string
    {
        $total = $this->total_stock;
        if ($total > $this->art_seuil_alerte) {
            return 'Disponible';
        }
        return $total > 0 ? 'Stock bas' : 'Rupture';
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Charge la catégorie, les stocks par entrepôt et calcule le stock total
     * en une seule requête GROUP BY via withSum().
     * À utiliser systématiquement pour éviter le problème N+1.
     */
    public function scopeWithStockDetails($query)
    {
        return $query
            ->with(['category', 'itemStocks.warehouse'])
            ->withSum('itemStocks as calculated_total_stock', 'sta_quantite');
    }

    /**
     * Filtre les articles dont le stock total est inférieur ou égal au seuil d'alerte.
     * Inclut withSum pour rendre total_stock disponible sans requête supplémentaire.
     */
    public function scopeBelowAlertThreshold($query)
    {
        return $query
            ->whereRaw('COALESCE((SELECT SUM(sta_quantite) FROM stocks_articles WHERE articles.art_id = stocks_articles.sta_art_id), 0) <= art_seuil_alerte')
            ->withSum('itemStocks as total_stock', 'sta_quantite');
    }

    // =========================================================================
    // MÉTHODES STATIQUES — LECTURE / REQUÊTES
    // =========================================================================

    /**
     * Calcule la valeur totale du stock global (quantité × prix par défaut).
     * Utilise une jointure SQL directe pour la performance.
     */
    public static function totalStockValue(): float
    {
        return (float) (
            DB::table('articles')
                ->join('stocks_articles', 'articles.art_id', '=', 'stocks_articles.sta_art_id')
                ->selectRaw('SUM(stocks_articles.sta_quantite * COALESCE(articles.art_prix_defaut, 0)) as total_value')
                ->value('total_value') ?? 0
        );
    }

    /**
     * Retourne les articles dont le stock est sous le seuil d'alerte.
     * Résultat : Collection filtrée en mémoire après un seul chargement.
     */
    public static function getUnderThreshold(): \Illuminate\Support\Collection
    {
        return self::withStockDetails()
            ->get()
            ->filter(fn ($article) => ($article->calculated_total_stock ?? 0) <= $article->art_seuil_alerte);
    }

    /**
     * Retourne les articles paginés avec tous leurs détails pour la vue gestionnaire.
     */
    public static function getPaginatedForManager(int $perPage = 3)
    {
        return self::withStockDetails()->orderByDesc('art_created_at')->paginate($perPage, ['*'], 'items');
    }

    /**
     * Retourne la liste formatée des articles pour la vue demandeur (avec stock et statut).
     */
    public static function forDemandeurDisplay(): Collection
    {
        return static::withStockDetails()
            ->get()
            ->map(fn ($article) => $article->formatForDemandeur());
    }

    /**
     * Retourne la liste simplifiée des articles pour le formulaire de demande de sortie
     * (id, nom, entrepôts disponibles avec quantités).
     */
    public static function asAvailableList(): Collection
    {
        return static::with(['itemStocks.warehouse'])
            ->withSum('itemStocks as calculated_total_stock', 'sta_quantite')
            ->get()
            ->map(fn ($article) => $article->formatAvailability());
    }

    /**
     * Retourne les articles allégés pour les menus déroulants (sans relations ni appends).
     */
    public static function getForDropdown(): Collection
    {
        return self::select(['art_id', 'art_nom', 'art_reference'])->get();
    }

    // =========================================================================
    // MÉTHODES STATIQUES — ÉCRITURE
    // =========================================================================

    /** Crée un article depuis les données validées du request. */
    public static function storeFromRequest(array $data): static
    {
        return self::create($data);
    }

    // =========================================================================
    // MÉTHODES D'INSTANCE — FORMATAGE
    // =========================================================================

    /**
     * Formate l'article pour la liste du demandeur (stock, statut, entrepôts).
     * Nécessite withStockDetails() en amont.
     */
    public function formatForDemandeur(): array
    {
        return [
            'id'         => $this->art_id,
            'code'       => $this->art_reference,
            'name'       => $this->art_nom,
            'category'   => $this->category->cat_nom ?? 'N/A',
            'stock'      => $this->total_stock,
            'status'     => $this->status,
            'warehouses' => $this->itemStocks->map(fn ($stock) => [
                'id'   => $stock->sta_ent_id,
                'name' => $stock->warehouse->ent_nom ?? 'N/A',
                'qty'  => $stock->sta_quantite,
            ])->values(),
        ];
    }

    /**
     * Formate l'article pour la liste de disponibilité (formulaire de demande).
     * Nécessite itemStocks.warehouse chargé en amont.
     */
    public function formatAvailability(): array
    {
        return [
            'id'         => $this->art_id,
            'nom'        => $this->art_nom,
            'warehouses' => $this->itemStocks->map(fn ($stock) => [
                'id'   => $stock->sta_ent_id,
                'name' => $stock->warehouse->ent_nom ?? 'N/A',
                'qty'  => $stock->sta_quantite,
            ])->values(),
        ];
    }

    // =========================================================================
    // MÉTHODES D'INSTANCE — LOGIQUE MÉTIER
    // =========================================================================

    /** Met à jour l'article depuis les données validées du request. */
    public function updateFromRequest(array $data): bool
    {
        return $this->update($data);
    }

    /**
     * Vérifie si un stock suffisant existe dans un entrepôt donné.
     * Déclenche une requête SQL — à utiliser ponctuellement (pas en boucle).
     */
    public function hasStock(int $warehouseId, int $quantity): bool
    {
        $stock = $this->itemStocks()->where('sta_ent_id', $warehouseId)->first();
        return $stock && $stock->sta_quantite >= $quantity;
    }

    // app/Models/Article.php
    protected $appends = ['total_stock'];
}