<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingNotification extends Notification
{
    use Queueable;

    protected $booking;
    protected $action;
    protected $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($booking, $action, $message)
    {
        $this->booking = $booking;
        $this->action = $action;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $url = route('admin.bookings.index');

        if (method_exists($notifiable, 'isOwner') && $notifiable->isOwner()) {
            $url = route('owner.bookings.index');
        }

        return [
            'booking_id' => $this->booking->id,
            'arena_name' => $this->booking->arena->name ?? 'Sân bóng',
            'action' => $this->action, // 'created', 'cancelled'
            'message' => $this->message,
            'date' => $this->booking->date,
            'url' => $url,
        ];
    }
}
