<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SuppliersSeeder extends Seeder
{
    public function run()
    {
        // Build suppliers from distinct brands present in product_variants
        $db = \Illuminate\Support\Facades\DB::connection();

        // disable foreign key checks to safely truncate when related records exist
        $db->statement('SET FOREIGN_KEY_CHECKS=0;');
        Supplier::truncate();

        // get distinct non-empty brands from product_variants
        $brands = $db->table('product_variants')
            ->select('brand')
            ->whereNotNull('brand')
            ->where('brand', '<>', '')
            ->distinct()
            ->pluck('brand');

        $created = [];
        foreach ($brands as $brand) {
            $s = Supplier::create([
                'name' => $brand,
                'phone' => null,
                'email' => null,
                'website' => null,
                'note' => 'Auto-generated from product_variants.brand',
            ]);
            $created[$brand] = $s->id;
        }

        // update product_variants.supplier_id for existing variants
        foreach ($created as $brand => $supplierId) {
            $db->table('product_variants')->where('brand', $brand)->update(['supplier_id' => $supplierId]);
        }

        $db->statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}