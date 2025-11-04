<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PurchaseOrderApproval extends Model
{
    use LogsActivity;

    protected $fillable = [
        'purchase_order_id',
        'approver_id',
        'approval_level_id',
        'status',
        'notes'
    ];

    protected $dates = ['approved_at'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function purchase_order()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function approver()
    {
        return $this->belongsTo(Approver::class);
    }

    public function approval_level()
    {
        return $this->belongsTo(ApprovalLevel::class, 'approval_level_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'notes', 'approved_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Purchase Order Approval has been {$eventName}");
    }
}
