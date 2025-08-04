<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPrice extends Model
{
    protected $fillable = [
        'supplier_id',
        'item_code',
        'item_description',
        'part_number',
        'brand',
        'project',
        'warehouse',
        'start_date',
        'expired_date',
        'uploaded_by',
        'uom',
        'qty',
        'price',
        'description',
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

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function histories()
    {
        return $this->hasMany(ItemPriceHistory::class, 'item_code', 'item_code')
            ->orderBy('start_date', 'desc');
    }
}
