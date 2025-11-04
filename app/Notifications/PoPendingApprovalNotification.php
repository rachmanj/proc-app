<?php

namespace App\Notifications;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PoPendingApprovalNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PurchaseOrder $purchaseOrder,
        public string $approvalLevel
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'po_pending_approval',
            'message' => "Purchase Order {$this->purchaseOrder->doc_num} is pending your approval ({$this->approvalLevel})",
            'po_id' => $this->purchaseOrder->id,
            'po_number' => $this->purchaseOrder->doc_num,
            'url' => route('approvals.po.show', $this->purchaseOrder),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
