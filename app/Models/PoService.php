<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PoService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_no',
        'date',
        'vendor_code',
        'project_code',
        'is_vat',
        'remarks',
        'print_count',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'date' => 'date',
        'is_vat' => 'boolean',
        'print_count' => 'integer'
    ];

    public function items()
    {
        return $this->hasMany(ItemService::class, 'po_service_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'vendor_code', 'code');
    }
}
