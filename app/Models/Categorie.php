<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    use HasFactory;

    protected $primaryKey = 'cat_id';

    public const CREATED_AT = 'cat_created_at';
    public const UPDATED_AT = 'cat_updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cat_nom',
        'cat_code',
        'cat_description',
    ];

    /**
     * Get the items for the category.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Article::class, 'art_cat_id', 'cat_id');
    }

    // Categories paginer avec les relations
    public static function getWithRelationsPaginated($perPage)
    {
        return self::with(['items'])->paginate($perPage, ['*'], 'categories');
    }

    // Dans Categorie.php
    public static function getAllCategories()
    {
        return self::all(['cat_id', 'cat_nom']);
    }
}

