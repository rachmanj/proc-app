<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApprovalStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PurchaseOrder $purchaseOrder,
        public string $status,
        public ?string $approverName = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusMessages = [
            'approved' => "Purchase Order {$this->purchaseOrder->doc_num} has been approved" . ($this->approverName ? " by {$this->approverName}" : ''),
            'rejected' => "Purchase Order {$this->purchaseOrder->doc_num} has been rejected" . ($this->approverName ? " by {$this->approverName}" : ''),
            'revision' => "Purchase Order {$this->purchaseOrder->doc_num} requires revision" . ($this->approverName ? " requested by {$this->approverName}" : ''),
        ];

        return [
            'type' => 'approval_status_changed',
            'message' => $statusMessages[$this->status] ?? "Purchase Order {$this->purchaseOrder->doc_num} status changed to {$this->status}",
            'po_id' => $this->purchaseOrder->id,
            'po_number' => $this->purchaseOrder->doc_num,
            'status' => $this->status,
            'url' => route('procurement.po.show', $this->purchaseOrder),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
