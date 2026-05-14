<?php

namespace App\Notifications;

use App\Models\OwnerRequest;
use Illuminate\Notifications\Notification;

class OwnerRequestNotification extends Notification
{
    public function __construct(
        protected OwnerRequest $ownerRequest,
        protected string $action,  // 'submitted' | 'approved' | 'rejected'
        protected string $message
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'owner_request_id' => $this->ownerRequest->id,
            'user_name'        => $this->ownerRequest->user->name ?? '',
            'action'           => $this->action,
            'message'          => $this->message,
            'url'              => $this->action === 'submitted'
                ? route('admin.owner-requests.index')
                : route('profile'),
        ];
    }
}
