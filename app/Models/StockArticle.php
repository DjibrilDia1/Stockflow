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
}

