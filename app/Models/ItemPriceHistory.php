<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPriceHistory extends Model
{
    protected $fillable = [
        'item_code',
        'item_description',
        'supplier_id',
        'project',
        'warehouse',
        'part_number',
        'brand',
        'price',
        'uom',
        'qty',
        'start_date',
        'expired_date',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expired_date' => 'date',
        'qty' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
