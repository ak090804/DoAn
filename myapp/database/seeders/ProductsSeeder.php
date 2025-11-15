<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $products = [
            //đồ uống có cồn
            ['name' => 'Bia hơi', 'category_id' => 1],
            ['name' => 'Rượu mạnh', 'category_id' => 1],
            ['name' => 'Rượu champagnes', 'category_id' => 1],
            ['name' => 'Rượu vang trắng', 'category_id' => 1],
            ['name' => 'Rượu vang đỏ', 'category_id' => 1],
            //trẻ sơ sinh
            ['name' => 'Tã giấy', 'category_id' => 2],
            ['name' => 'Sữa bột', 'category_id' => 2],
            ['name' => 'Phụ kiện cho bé', 'category_id' => 2],                        
            ['name' => 'Sữa tắm trẻ em', 'category_id' => 2],
            //bánh ngọt
            ['name' => 'Bánh mì', 'category_id' => 3],
            ['name' => 'Bánh bao cuộn', 'category_id' => 3],
            ['name' => 'Bánh tortilla', 'category_id' => 3],
            ['name' => 'Bánh mì sandwich', 'category_id' => 3],
            //nước giải khát
            ['name' => 'Trà', 'category_id' => 4],
            ['name' => 'Nước khoáng', 'category_id' => 4],
            ['name' => 'Cà phê', 'category_id' => 4],
            ['name' => 'Đồ uống lạnh', 'category_id' => 4],
            ['name' => 'Nước ngọt', 'category_id' => 4],
            ['name' => 'Nước ép trái cây', 'category_id' => 4],
            ['name' => 'Nước tăng lực thể thao', 'category_id' => 4],
            ['name' => 'Hỗn hợp đồ uống ca cao', 'category_id' => 4],
            //bữa sáng
            ['name' => 'Bột ngũ cốc', 'category_id' => 5],
            ['name' => 'Ngũ cốc nóng', 'category_id' => 5],
            ['name' => 'Granola', 'category_id' => 5],
            ['name' => 'Lương khô', 'category_id' => 5],
            // Nguyên liệu số lượng lớn
            ['name' => 'Ngũ cốc', 'category_id' => 6],
            ['name' => 'Trái cây sấy khô', 'category_id' => 6],
            // Đồ hộp
            ['name' => 'Đậu đóng hộp', 'category_id' => 7],
            ['name' => 'Sốt táo đóng hộp', 'category_id' => 7],
            ['name' => 'Rau củ đóng hộp', 'category_id' => 7],
            ['name' => 'Súp đóng hộp', 'category_id' => 7],
            ['name' => 'Thịt hộp', 'category_id' => 7],
            // Sữa, trứng
            ['name' => 'Đậu nành không lactose', 'category_id' => 8],
            ['name' => 'Bơ', 'category_id' => 8],
            ['name' => 'Sữa chua', 'category_id' => 8],
            ['name' => 'Sữa tươi', 'category_id' => 8],
            ['name' => 'Phô mai đóng gói', 'category_id' => 8],
            ['name' => 'Trứng', 'category_id' => 8],
            ['name' => 'Kem tươi', 'category_id' => 8],
            ['name' => 'Kem sữa', 'category_id' => 8],
            ['name' => 'Bánh pudding', 'category_id' => 8],
            ['name' => 'Phô mai đặc biệt', 'category_id' => 8],
            // Đồ nguội
            ['name' => 'Thịt nguội', 'category_id' => 9],
            ['name' => 'Nước chấm', 'category_id' => 9],
            ['name' => 'Nước sốt salad', 'category_id' => 9],
            ['name' => 'Bữa ăn chế biến sẵn', 'category_id' => 9],
            ['name' => 'Đậu hũ', 'category_id' => 9],
            // Mì
            ['name' => 'Mì tươi', 'category_id' => 10],
            ['name' => 'Nước sốt mì', 'category_id' => 10],
            ['name' => 'Mì khô', 'category_id' => 10],
            ['name' => 'Mì ngũ cốc', 'category_id' => 10],
            ['name' => 'Mì ăn liền', 'category_id' => 10],
            // Đồ gia dụng
            ['name' => 'Đĩa sứ', 'category_id' => 11],
            ['name' => 'Đĩa giấy', 'category_id' => 11],
            ['name' => 'Màng bọc thực phẩm', 'category_id' => 11],
            ['name' => 'Bột giặt', 'category_id' => 11],
            ['name' => 'Nước hoa xịt phòng', 'category_id' => 11],
            ['name' => 'Nước tẩy bồn cầu', 'category_id' => 11],
            ['name' => 'Túi rác', 'category_id' => 11],
            ['name' => 'Nước rửa chén', 'category_id' => 11],
            ['name' => 'Dao', 'category_id' => 11],
            ['name' => 'Chảo', 'category_id' => 11],
            // Thịt & hải sản
            ['name' => 'Thịt gà tươi', 'category_id' => 12],
            ['name' => 'Thịt gà đóng gói', 'category_id' => 12],
            ['name' => 'Xúc xích xông khói', 'category_id' => 12],
            ['name' => 'Thịt heo tươi', 'category_id' => 12],
            ['name' => 'Cá ngừ đóng gói', 'category_id' => 12],
            ['name' => 'Thịt đóng gói', 'category_id' => 12],
            ['name' => 'Hải sản tươi sống', 'category_id' => 12],
            //Nguyên liệu nấu ăn
            ['name' => 'Bột làm bánh', 'category_id' => 13],
            ['name' => 'Đồ ngâm chua & ô liu', 'category_id' => 13],
            ['name' => 'Siro & mật ong', 'category_id' => 13],
            ['name' => 'Muối', 'category_id' => 13],
            ['name' => 'Bơ phết bánh mì', 'category_id' => 13],
            ['name' => 'Đường', 'category_id' => 13],
            ['name' => 'Bơ phết', 'category_id' => 13],
            ['name' => 'Nước sốt salad', 'category_id' => 13],
            ['name' => 'Gelatin', 'category_id' => 13],
            ['name' => 'Giấm', 'category_id' => 13],
            ['name' => 'Nước ướp thịt', 'category_id' => 13],
            ['name' => 'Đồ trang trí bánh ngọt', 'category_id' => 13],
            //Chăm sóc cá nhân
            ['name' => 'Kem đánh răng', 'category_id' => 14],
            ['name' => 'Xà phòng', 'category_id' => 14],
            ['name' => 'Miếng dán giảm đau', 'category_id' => 14],
            ['name' => 'Thuốc cảm cúm', 'category_id' => 14],
            ['name' => 'Băng vệ sinh', 'category_id' => 14],
            ['name' => 'Thuốc nhỏ mắt', 'category_id' => 14],
            ['name' => 'Dao cạo râu', 'category_id' => 14],
            ['name' => 'Kem dưỡng da', 'category_id' => 14],
            ['name' => 'Lăn khử mùi', 'category_id' => 14],
            ['name' => 'Protein thay thế bữa ăn', 'category_id' => 14],
            ['name' => 'Men tiêu hóa', 'category_id' => 14],
            ['name' => 'Sữa dưỡng thể', 'category_id' => 14],
            ['name' => 'Dầu gội', 'category_id' => 14],
            ['name' => 'Vitamin', 'category_id' => 14],
            ['name' => 'Hộp sơ cứu', 'category_id' => 14],
            ['name' => 'Mặt nạ dưỡng da', 'category_id' => 14],
            ['name' => 'Kem dưỡng da', 'category_id' => 14],
            //thú cưng
            ['name' => 'Thức ăn cho chó', 'category_id' => 15],
            ['name' => 'Thức ăn cho mèo', 'category_id' => 15],        
            //nông sản tươi
            ['name' => 'Bắp cải', 'category_id' => 16],
            ['name' => 'Táo', 'category_id' => 16],   
            ['name' => 'Bắp cải đóng gói', 'category_id' => 16],
            ['name' => 'Táo đóng hộp', 'category_id' => 16],  
            ['name' => 'Rau thơm', 'category_id' => 16],
            //Đồ ăn nhẹ
            ['name' => 'Thanh năng lượng', 'category_id' => 17],
            ['name' => 'Hạt dẻ', 'category_id' => 17],
            ['name' => 'Khoai tây chiên', 'category_id' => 17],
            ['name' => 'Bắp rang bơ', 'category_id' => 17],
            ['name' => 'Bánh quy', 'category_id' => 17],
            ['name' => 'Kẹo chocolate', 'category_id' => 17],
            ['name' => 'Kẹo trái cây', 'category_id' => 17],
            ['name' => 'Kẹo cao su bạc hà', 'category_id' => 17],
            ['name' => 'Snack hạt hỗn hợp', 'category_id' => 17],
            ['name' => 'Bánh ngọt', 'category_id' => 17],
            ['name' => 'Bánh kem', 'category_id' => 17],
        ];

        DB::table('products')->insert($products);
    }
}
