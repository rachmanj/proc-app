<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PoAttachment extends Model
{
    protected $fillable = [
        'original_name',
        'file_path',
        'description'
    ];

    public function purchaseOrders()
    {
        return $this->belongsToMany(PurchaseOrder::class, 'po_attachment_purchase_order')
            ->withTimestamps();
    }

    // Helper method to store file
    public static function storeFile($file, $description = null)
    {
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store('po-attachments', 'public');

        return self::create([
            'original_name' => $fileName,
            'file_path' => $filePath,
            'description' => $description
        ]);
    }
}
