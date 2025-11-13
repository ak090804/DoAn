<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\ImportNote;
use App\Models\ImportNoteItem;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ImportNotesSeeder extends Seeder
{
    public function run()
    {
        $employees = Employee::all();
        $products = ProductVariant::all();

        // Create 5 sample import notes
        for ($i = 0; $i < 5; $i++) {
            $note = ImportNote::create([
                'employee_id' => $employees->random()->id,
                'total_price' => 0,
                'status' => ['pending', 'approved', 'cancelled'][rand(0, 2)],
                'note' => 'Sample import note #' . ($i + 1),
            ]);

            $total = 0;
            // Add 2-4 items to each note
            for ($j = 0; $j < rand(2, 4); $j++) {
                $pv = $products->random();
                $quantity = rand(1, 10);
                $price = $pv->price * 0.7; // Import price is 70% of selling price
                $subtotal = $quantity * $price;
                $total += $subtotal;

                ImportNoteItem::create([
                    'import_note_id' => $note->id,
                    'product_variant_id' => $pv->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);
            }

            $note->update(['total_price' => $total]);
        }
    }
}