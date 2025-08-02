<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderApproval extends Model
{
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
}
