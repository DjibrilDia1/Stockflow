<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\StockArticle;
use App\Models\Article;
use App\Models\Entrepot;
use App\Models\Fournisseur;

class MouvementStock extends Model
{
    use HasFactory;

    protected $table = 'mouvements_stock';
    protected $primaryKey = 'mvs_id';

    public const CREATED_AT = 'mvs_created_at';
    public const UPDATED_AT = 'mvs_updated_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mvs_date_mouvement' => 'datetime',
    ];

    /**
     * Get data for the manager's movement index.
     */
    public static function getIndexData()
    {
        return [
            'stockMovements' => self::withFullDetails()
                ->latest('mvs_date_mouvement')
                ->paginate(3),
            'articles' => Article::all(['art_id', 'art_nom', 'art_reference']),
            'warehouses' => Entrepot::all(['ent_id', 'ent_nom']),
            'suppliers' => Fournisseur::all(['fou_id', 'fou_nom']),
        ];
    }

    /**
     * Record a movement and update the stock accordingly.
     */
    public static function recordMovement(array $data): self
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            // 1. Enregistrer le mouvement
            $movement = self::create($data);

            // 2. Mettre à jour le stock dans l'entrepôt source
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

            // 3. Si c'est un transfert, ajouter dans l'entrepôt de destination
            if ($data['mvs_type'] === 'TRANSFER' && isset($data['mvs_ent_dest_id'])) {
                $stockDest = StockArticle::firstOrCreate(
                    ['sta_art_id' => $data['mvs_art_id'], 'sta_ent_id' => $data['mvs_ent_dest_id']],
                    ['sta_quantite' => 0]
                );
                $stockDest->sta_quantite += $data['mvs_quantite'];
                $stockDest->save();

                // On crée un deuxième mouvement de type "IN" pour la destination
                self::create([
                    'mvs_art_id' => $data['mvs_art_id'],
                    'mvs_ent_id' => $data['mvs_ent_dest_id'],
                    'mvs_type' => 'IN',
                    'mvs_quantite' => $data['mvs_quantite'],
                    'mvs_motif' => "Transfert depuis l'entrepôt ID: " . $data['mvs_ent_id'] . ". " . ($data['mvs_motif'] ?? ''),
                    'mvs_date_mouvement' => $data['mvs_date_mouvement'],
                    'mvs_usr_id' => $data['mvs_usr_id'],
                    'mvs_transfer_id' => $movement->mvs_id,
                ]);
            }

            return $movement;
        });
    }

    /**
     * Format the movement for the list.
     */
    public function formatForList(): array
    {
        return [
            'id' => $this->mvs_id,
            'date' => $this->mvs_date_mouvement->format('d/m/Y'),
            'article' => $this->item->art_nom ?? 'N/A',
            'type' => $this->mvs_type,
            'quantity' => $this->mvs_type === 'OUT' ? -$this->mvs_quantite : $this->mvs_quantite,
            'entrepot' => $this->warehouse->ent_nom ?? 'N/A',
            'service' => $this->service->ser_nom ?? 'N/A'
        ];
    }

    /**
     * Scope to eager load all details.
     */
    public function scopeWithFullDetails($query)
    {
        return $query->with(['item', 'warehouse', 'supplier', 'service', 'user']);
    }

    /**
     * Get the top consumed articles.
     */
    public static function getTopConsumed($limit = 5)
    {
        return self::where('mvs_type', 'OUT')
            ->selectRaw('mvs_art_id, sum(mvs_quantite) as total_qty')
            ->groupBy('mvs_art_id')
            ->orderByDesc('total_qty')
            ->take($limit)
            ->with('item')
            ->get();
    }

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
}

