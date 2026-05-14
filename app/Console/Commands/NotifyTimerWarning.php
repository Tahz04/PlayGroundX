<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class NotifyTimerWarning extends Command
{
    protected $signature   = 'bookings:auto-complete';
    protected $description = 'Tự động hoàn thành các booking đã qua ngày mà chưa được xử lý';

    public function handle(): void
    {
        $updated = Booking::whereIn('status', ['confirmed', 'paid'])
            ->where('date', '<', now()->toDateString())
            ->update(['status' => 'completed']);

        if ($updated > 0) {
            $this->info("Đã tự động hoàn thành {$updated} booking.");
        }
    }
}
