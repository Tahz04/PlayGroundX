<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Arena;

class FootballArenaSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'Hà Nội' => ['lat' => 21.028511, 'lng' => 105.804817],
            'Đà Nẵng' => ['lat' => 16.054407, 'lng' => 108.202164],
            'Huế' => ['lat' => 16.463713, 'lng' => 107.590866],
            'Đà Lạt' => ['lat' => 11.940419, 'lng' => 108.458313],
            'Hồ Chí Minh' => ['lat' => 10.823099, 'lng' => 106.629662],
        ];

        $types = ['Sân 5', 'Sân 7', 'Sân 11'];

        $arenaNames = [
            'Sân Bóng Đá Cỏ Nhân Tạo',
            'Sân Bóng Mini',
            'Trung Tâm Thể Thao',
            'Sân Bóng Thanh Niên',
            'Sân Bóng Sao Mai',
            'Sân Bóng Quyết Thắng',
            'Sân Thể Thao Đa Năng',
            'Sân Bóng Đoàn Kết',
            'Khu Thể Thao',
            'Sân Bóng Phố Núi'
        ];

        $cities = array_keys($locations);

        for ($i = 1; $i <= 20; $i++) {
            $city = $cities[array_rand($cities)];
            $type = $types[array_rand($types)];
            $namePrefix = $arenaNames[array_rand($arenaNames)];

            $lat = $locations[$city]['lat'] + (rand(-100, 100) / 10000);
            $lng = $locations[$city]['lng'] + (rand(-100, 100) / 10000);

            Arena::create([
                'name' => $namePrefix . ' ' . $i,
                'type' => $type,
                'location' => 'Đường số ' . rand(1, 100) . ', ' . $city,
                'price' => rand(20, 50) * 10000,
                'latitude' => $lat,
                'longitude' => $lng,
                'status' => 'active',
            ]);
        }
    }
}
