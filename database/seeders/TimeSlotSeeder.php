<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TimeSlot;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slots = [
            ['start_time' => '06:00:00', 'end_time' => '07:30:00'],
            ['start_time' => '07:30:00', 'end_time' => '09:00:00'],
            ['start_time' => '09:00:00', 'end_time' => '10:30:00'],
            ['start_time' => '16:00:00', 'end_time' => '17:30:00'],
            ['start_time' => '17:30:00', 'end_time' => '19:00:00'],
            ['start_time' => '19:00:00', 'end_time' => '20:30:00'],
            ['start_time' => '20:30:00', 'end_time' => '22:00:00'],
        ];

        foreach ($slots as $slot) {
            TimeSlot::create($slot);
        }
    }
}
