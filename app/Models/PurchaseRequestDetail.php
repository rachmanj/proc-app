<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseRequestDetail extends Model
{
    protected $fillable = [
        'purchase_request_id',
        'item_code',
        'item_name',
        'quantity',
        'uom',
        'open_qty',
        'line_remarks',
        'status'
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }
}
