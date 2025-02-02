<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'doc_num',
        'doc_date',
        'create_date',
        'supplier_name',
        'status'
    ];

    protected $casts = [
        'doc_date' => 'date',
        'create_date' => 'date',
    ];

    public function attachments(): BelongsToMany
    {
        return $this->belongsToMany(PoAttachment::class, 'po_attachment_purchase_order')
            ->withTimestamps();
    }

    // Helper method to attach files
    public function attachFiles($files, $descriptions = [])
    {
        $attachments = [];

        foreach ($files as $index => $file) {
            try {
                $path = $file->store('po-attachments', 'public');

                $attachment = PoAttachment::create([
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'description' => $descriptions[$index] ?? null,
                ]);

                $this->attachments()->attach($attachment->id);
                $attachments[] = $attachment;
            } catch (\Exception $e) {
                Log::error('Error attaching file: ' . $e->getMessage());
                continue;
            }
        }

        return $attachments;
    }
}
