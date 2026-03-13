<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
    use HasFactory;

    protected $table = 'fournisseurs';
    protected $primaryKey = 'fou_id';

    public const CREATED_AT = 'fou_created_at';
    public const UPDATED_AT = 'fou_updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fou_nom',
        'fou_contact_nom',
        'fou_telephone',
        'fou_email',
        'fou_adresse',
        'fou_categorie',
    ];

    /**
     * Get the stock movements for the supplier.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'mvs_fou_id', 'fou_id');
    }
}

