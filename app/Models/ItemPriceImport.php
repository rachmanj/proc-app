<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPriceImport extends Model
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
        'uom',
        'qty',
        'price',
        'description',
        'import_batch',
        'status',
        'error_message',
        'uploaded_by',
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
}
