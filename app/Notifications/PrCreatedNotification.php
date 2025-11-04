<?php

namespace App\Notifications;

use App\Models\PurchaseRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PrCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PurchaseRequest $purchaseRequest
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'pr_created',
            'message' => "New Purchase Request {$this->purchaseRequest->pr_no ?? $this->purchaseRequest->pr_draft_no} has been created",
            'pr_id' => $this->purchaseRequest->id,
            'pr_no' => $this->purchaseRequest->pr_no ?? $this->purchaseRequest->pr_draft_no,
            'url' => route('procurement.pr.show', $this->purchaseRequest),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
