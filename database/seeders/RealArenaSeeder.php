<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Arena;

class RealArenaSeeder extends Seeder
{
    public function run(): void
    {
        $arenas = [
            // Hà Nội
            [
                'name' => 'Sân bóng đá Thủy Lợi',
                'location' => '175 Tây Sơn, Đống Đa, Hà Nội',
                'latitude' => 21.007626,
                'longitude' => 105.824513,
            ],
            [
                'name' => 'Sân bóng đá Đại học Y',
                'location' => '1 Tôn Thất Tùng, Đống Đa, Hà Nội',
                'latitude' => 21.001648,
                'longitude' => 105.829141,
            ],
            [
                'name' => 'Sân bóng đá Chùa Láng',
                'location' => '112 Chùa Láng, Đống Đa, Hà Nội',
                'latitude' => 21.022987,
                'longitude' => 105.803732,
            ],
            [
                'name' => 'Sân bóng VSA',
                'location' => '5D Lê Trọng Tấn, Khương Mai, Thanh Xuân, Hà Nội',
                'latitude' => 20.998421,
                'longitude' => 105.829584,
            ],

            // TP.HCM
            [
                'name' => 'Sân bóng đá Chảo Lửa',
                'location' => '30 Phan Thúc Duyện, Phường 4, Tân Bình, TP.HCM',
                'latitude' => 10.803965,
                'longitude' => 106.661612,
            ],
            [
                'name' => 'Sân bóng đá Thăng Long',
                'location' => '76/12B Cộng Hòa, Phường 4, Tân Bình, TP.HCM',
                'latitude' => 10.801648,
                'longitude' => 106.656914,
            ],
            [
                'name' => 'Sân bóng đá Tao Đàn',
                'location' => '1 Huyền Trân Công Chúa, Phường Bến Thành, Quận 1, TP.HCM',
                'latitude' => 10.774982,
                'longitude' => 106.693951,
            ],
            [
                'name' => 'Sân bóng Kỳ Hòa',
                'location' => '824/28S Sư Vạn Hạnh, Phường 12, Quận 10, TP.HCM',
                'latitude' => 10.775837,
                'longitude' => 106.668541,
            ],
            [
                'name' => 'Sân bóng đá Phúc Yên',
                'location' => '31 Phan Huy Ích, Phường 15, Tân Bình, TP.HCM',
                'latitude' => 10.832961,
                'longitude' => 106.634712,
            ],
            [
                'name' => 'Sân bóng Celadon City',
                'location' => '68 Đường N1, Sơn Kỳ, Tân Phú, TP.HCM',
                'latitude' => 10.804135,
                'longitude' => 106.615967,
            ],

            // Đà Nẵng
            [
                'name' => 'Sân bóng đá Chuyên Việt',
                'location' => '98 Tiểu La, Hòa Cường Bắc, Hải Châu, Đà Nẵng',
                'latitude' => 16.044192,
                'longitude' => 108.216348,
            ],
            [
                'name' => 'Sân bóng Tuyên Sơn',
                'location' => '22 Đường 2/9, Hòa Cường Bắc, Hải Châu, Đà Nẵng',
                'latitude' => 16.035129,
                'longitude' => 108.225916,
            ],
            [
                'name' => 'Sân bóng đá Lê Độ',
                'location' => '119 Lê Độ, Chính Gián, Thanh Khê, Đà Nẵng',
                'latitude' => 16.064512,
                'longitude' => 108.196721,
            ],
            [
                'name' => 'Sân bóng Phước Mỹ',
                'location' => '42 Phước Mỹ 1, Phước Mỹ, Sơn Trà, Đà Nẵng',
                'latitude' => 16.063185,
                'longitude' => 108.243516,
            ],

            // Huế
            [
                'name' => 'Sân bóng An Cựu City',
                'location' => 'Khu đô thị An Cựu City, An Đông, TP. Huế',
                'latitude' => 16.449621,
                'longitude' => 107.603951,
            ],
            [
                'name' => 'Sân bóng Uyên Phương',
                'location' => '150 Nguyễn Trãi, Tây Lộc, TP. Huế',
                'latitude' => 16.475135,
                'longitude' => 107.573618,
            ],
            [
                'name' => 'Sân cỏ nhân tạo Xuân Phú',
                'location' => 'Tố Hữu, Xuân Phú, TP. Huế',
                'latitude' => 16.459341,
                'longitude' => 107.600125,
            ],

            // Đà Lạt
            [
                'name' => 'Sân bóng đá Cao đẳng Sư phạm',
                'location' => '29 Yersin, Phường 10, TP. Đà Lạt',
                'latitude' => 11.942183,
                'longitude' => 108.455219,
            ],
            [
                'name' => 'Sân bóng đá Phù Đổng',
                'location' => '34 Phù Đổng Thiên Vương, Phường 8, TP. Đà Lạt',
                'latitude' => 11.956812,
                'longitude' => 108.445217,
            ],
            [
                'name' => 'Sân bóng Bùi Thị Xuân',
                'location' => '73 Bùi Thị Xuân, Phường 2, TP. Đà Lạt',
                'latitude' => 11.946351,
                'longitude' => 108.439812,
            ],
        ];

        $types = ['Sân 5', 'Sân 7', 'Sân 11'];

        foreach ($arenas as $arenaData) {
            Arena::create([
                'name' => $arenaData['name'],
                'type' => $types[array_rand($types)],
                'location' => $arenaData['location'],
                'price' => rand(20, 50) * 10000,
                'latitude' => $arenaData['latitude'],
                'longitude' => $arenaData['longitude'],
                'status' => 'active',
            ]);
        }
    }
}
