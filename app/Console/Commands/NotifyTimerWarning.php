<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyTimerWarning extends Command
{
    protected $signature   = 'timers:notify-warning';
    protected $description = 'Gửi cảnh báo Telegram khi booking còn ≤ 15 phút';

    public function handle(): void
    {
        $bookings = Booking::with(['arena', 'user'])
            ->whereNotNull('timer_started_at')
            ->where('timer_notified', false)
            ->whereIn('status', ['confirmed', 'paid'])
            ->where('date', now()->toDateString())
            ->get();

        foreach ($bookings as $booking) {
            if (!$booking->end_time) {
                continue;
            }

            $endDateTime      = Carbon::parse($booking->date . ' ' . $booking->end_time);
            $minutesRemaining = (int) now()->diffInMinutes($endDateTime, false);

            if ($minutesRemaining <= 15 && $minutesRemaining > 0) {
                TelegramService::sendMessage(
                    "⏰ <b>NHẮC NHỞ THỜI GIAN</b>\n" .
                    "⚽ <b>Sân:</b> {$booking->arena->name}\n" .
                    "👤 <b>Khách:</b> {$booking->user->name}\n" .
                    "🕐 <b>Còn lại:</b> <b>{$minutesRemaining} phút</b>\n" .
                    "🏁 <b>Kết thúc lúc:</b> {$endDateTime->format('H:i')}"
                );

                $booking->update(['timer_notified' => true]);
            }
        }
    }
}
