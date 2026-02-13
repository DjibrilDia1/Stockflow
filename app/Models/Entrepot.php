<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entrepot extends Model
{
    use HasFactory;

    protected $table = 'entrepots';
    protected $primaryKey = 'ent_id';

    public const CREATED_AT = 'ent_created_at';
    public const UPDATED_AT = 'ent_updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ent_nom',
        'ent_code',
        'ent_localisation',
    ];

    /**
     * Get the item stocks for the warehouse.
     */
    public function itemStocks(): HasMany
    {
        return $this->hasMany(StockArticle::class, 'sta_ent_id', 'ent_id');
    }

    /**
     * Get the stock movements for the warehouse.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'mvs_ent_id', 'ent_id');
    }
}

