<?php

namespace App\Exports;

use App\Models\MouvementStock;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class MouvementsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = MouvementStock::with(['item', 'warehouse', 'supplier', 'service', 'user'])
            ->orderBy('mvs_date_mouvement', 'desc');

        if ($this->request->filled('article')) {
            $query->where('mvs_art_id', $this->request->article);
        }

        if ($this->request->filled('entrepot')) {
            $query->where('mvs_ent_id', $this->request->entrepot);
        }

        if ($this->request->filled('type')) {
            $query->where('mvs_type', $this->request->type);
        }

        if ($this->request->filled('date_start')) {
            $query->whereDate('mvs_date_mouvement', '>=', $this->request->date_start);
        }

        if ($this->request->filled('date_end')) {
            $query->whereDate('mvs_date_mouvement', '<=', $this->request->date_end);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Date',
            'Article',
            'Entrepôt',
            'Type',
            'Quantité',
            'Motif',
            'Fournisseur / Service',
            'Auteur',
        ];
    }

    public function map($movement): array
    {
        $source = '';
        if ($movement->mvs_type === 'IN') {
            $source = $movement->supplier ? $movement->supplier->fou_nom : '';
        } elseif ($movement->mvs_type === 'OUT') {
            $source = $movement->service ? $movement->service->ser_nom : '';
        }

        return [
            $movement->mvs_id,
            $movement->mvs_date_mouvement->format('d/m/Y H:i'),
            $movement->item->art_nom,
            $movement->warehouse->ent_nom,
            $movement->mvs_type,
            $movement->mvs_quantite,
            $movement->mvs_motif,
            $source,
            $movement->user ? $movement->user->name : '',
        ];
    }
}
