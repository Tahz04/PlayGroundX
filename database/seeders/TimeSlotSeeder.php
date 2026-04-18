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
        for ($hour = 6; $hour < 24; $hour++) {
            $startTime = sprintf('%02d:00:00', $hour);
            $endTime = $hour === 23 ? '00:00:00' : sprintf('%02d:00:00', $hour + 1);

            TimeSlot::updateOrCreate(
                [
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                ],
                []
            );
        }
    }
}
