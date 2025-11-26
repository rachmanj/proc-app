<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'sap_doc_entry',
        'sap_line_num',
        'sap_vis_order',
        'line_identity',
        'item_code',
        'item_name',
        'quantity',
        'uom',
        'open_qty',
        'line_remarks',
        'purchase_order_detail_id',
        'status'
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function purchaseOrderDetail()
    {
        return $this->belongsTo(PurchaseOrderDetail::class);
    }
}
