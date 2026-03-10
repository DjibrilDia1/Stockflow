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
     *
     * @var array<int, string>
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
     *
     * @var array
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


}
