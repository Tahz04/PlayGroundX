<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Arena;

class ArenaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $arenas = [
            [
                'name' => 'Sân Cầu Lông Kỳ Hòa',
                'location' => '12 Đường 3/2, Quận 10, TP.HCM',
                'price' => 80000,
                'latitude' => 10.776643,
                'longitude' => 106.671542,
                'status' => true,
            ],
            [
                'name' => 'Sân Cầu Lông Celadon City',
                'location' => '30 Bờ Bao Tân Thắng, Tân Phú, TP.HCM',
                'price' => 100000,
                'latitude' => 10.803789,
                'longitude' => 106.613912,
                'status' => true,
            ],
            [
                'name' => 'Sân Cầu Lông Thống Nhất',
                'location' => '138 Đào Duy Từ, Quận 10, TP.HCM',
                'price' => 75000,
                'latitude' => 10.760721,
                'longitude' => 106.663123,
                'status' => true,
            ],
            [
                'name' => 'Sân Cầu Lông Đào Duy Anh',
                'location' => '21 Đào Duy Anh, Phú Nhuận, TP.HCM',
                'price' => 90000,
                'latitude' => 10.798123,
                'longitude' => 106.675789,
                'status' => true,
            ],
            [
                'name' => 'Sân Cầu Lông Chu Văn An',
                'location' => 'Số 2 Đường số 1, Bình Thạnh, TP.HCM',
                'price' => 85000,
                'latitude' => 10.814234,
                'longitude' => 106.702567,
                'status' => true,
            ],
        ];

        foreach ($arenas as $arena) {
            Arena::create($arena);
        }
    }
}
