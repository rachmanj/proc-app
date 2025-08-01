<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PrAttachment extends Model
{
    protected $fillable = [
        'original_name',
        'file_path',
        'description',
        'keterangan',
        'pr_no',
        'file_type',
        'file_size'
    ];

    public function purchaseRequests(): BelongsToMany
    {
        return $this->belongsToMany(PurchaseRequest::class, 'pr_attachment_purchase_request')
            ->withTimestamps();
    }
} 