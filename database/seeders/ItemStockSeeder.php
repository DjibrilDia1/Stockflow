<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $itemStylo = Item::where('ref', 'STYLO-BLEU-001')->firstOrFail();
        $itemPc = Item::where('ref', 'PC-PORT-HP-005')->firstOrFail();
        $itemToner = Item::where('ref', 'TONER-LASER-30A')->firstOrFail();
        $itemCahier = Item::where('ref', 'CAHIER-A4-CARRE')->firstOrFail();

        $warehouseMain = Warehouse::where('code', 'ENT-MAIN')->firstOrFail();
        $warehouseAnnexe = Warehouse::where('code', 'ANN-MAG')->firstOrFail();

        // Stock for Stylo Bille Bleu
        ItemStock::create([
            'item_id' => $itemStylo->id,
            'warehouse_id' => $warehouseMain->id,
            'quantity' => 100,
        ]);
        ItemStock::create([
            'item_id' => $itemStylo->id,
            'warehouse_id' => $warehouseAnnexe->id,
            'quantity' => 50,
        ]);

        // Stock for Ordinateur Portable HP ProBook
        ItemStock::create([
            'item_id' => $itemPc->id,
            'warehouse_id' => $warehouseMain->id,
            'quantity' => 10,
        ]);

        // Stock for Cartouche Toner Laser 30A
        ItemStock::create([
            'item_id' => $itemToner->id,
            'warehouse_id' => $warehouseMain->id,
            'quantity' => 20,
        ]);
        ItemStock::create([
            'item_id' => $itemToner->id,
            'warehouse_id' => $warehouseAnnexe->id,
            'quantity' => 5,
        ]);

        // Stock for Cahier A4 Grands Carreaux
        ItemStock::create([
            'item_id' => $itemCahier->id,
            'warehouse_id' => $warehouseMain->id,
            'quantity' => 150,
        ]);
    }
}
