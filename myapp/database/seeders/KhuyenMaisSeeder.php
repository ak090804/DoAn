<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KhuyenMai;

class KhuyenMaisSeeder extends Seeder
{
    public function run()
    {
        // Generate 10 promotions per type (mix public/private)
        $types = [
            'tang_sp' => 'Tặng sản phẩm',
            'giam_gia_sp' => 'Giảm giá sản phẩm',
            'giam_gia_hd' => 'Giảm giá hoá đơn'
        ];

        // prefix map to ensure unique codes across types
        $prefixMap = [
            'tang_sp' => 'TS',
            'giam_gia_sp' => 'GSP',
            'giam_gia_hd' => 'GHD'
        ];

        foreach ($types as $typeKey => $typeLabel) {
            for ($i = 1; $i <= 10; $i++) {
                $isPrivate = ($i % 4 === 0); // every 4th one is private
                $prefix = $prefixMap[$typeKey] ?? strtoupper(substr($typeKey, 0, 3));
                $code = $prefix . sprintf('%02d', $i) . ($isPrivate ? 'P' : '');
                $name = "$typeLabel #$i" . ($isPrivate ? ' (Private)' : '');
                $description = $typeLabel . ' mẫu tự động ' . $i;

                $giaTri = 0;
                $soTienCap = null;
                $data = null;

                if ($typeKey === 'giam_gia_sp') {
                    // percent off for product-level (e.g., 5%..50%)
                    $giaTri = rand(5, 50);
                    $data = json_encode(['product_ids' => []]);
                } elseif ($typeKey === 'giam_gia_hd') {
                    // fixed discount amounts for order-level (e.g., 10000..100000)
                    $giaTri = rand(10000, 100000);
                    // occasionally cap the discount
                    if ($i % 3 === 0) {
                        $soTienCap = rand(20000, 50000);
                    }
                } elseif ($typeKey === 'tang_sp') {
                    // gift: assign a product_variant_id cycling through 1-10
                    $giftVariantId = ($i % 10) + 1;
                    $data = json_encode(['gift_product_variant_id' => $giftVariantId]);
                }

                KhuyenMai::create([
                    'ma' => $isPrivate ? null : $code,
                    'ten' => $name,
                    'mo_ta' => $description,
                    'loai' => $typeKey,
                    'gia_tri' => $giaTri,
                    'so_tien_giam_toi_da' => $soTienCap,
                    'is_private' => $isPrivate,
                    'data' => $data,
                    'active' => true,
                ]);
            }
        }
    }
}
