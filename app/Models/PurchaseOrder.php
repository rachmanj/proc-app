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

    public function approvals()
    {
        return $this->hasMany(PurchaseOrderApproval::class);
    }

    public function submit()
    {
        $this->status = 'submitted';
        $this->save();

        // Create first level approval record
        $firstLevel = ApprovalLevel::where('level', 1)->first();
        $this->approvals()->create([
            'approval_level_id' => $firstLevel->id,
            'status' => 'pending'
        ]);
    }

    public function approve($approverId, $notes = null)
    {
        $currentApproval = $this->approvals()->where('status', 'pending')->first();
        
        if (!$currentApproval) {
            return false;
        }

        $currentApproval->update([
            'status' => 'approved',
            'approver_id' => $approverId,
            'notes' => $notes,
            'approved_at' => now()
        ]);

        if ($currentApproval->approvalLevel->level === 1) {
            $this->status = 'approved_level_1';
            $this->save();

            // Create next level approval
            $nextLevel = ApprovalLevel::where('level', 2)->first();
            $this->approvals()->create([
                'approval_level_id' => $nextLevel->id,
                'status' => 'pending'
            ]);
        } else {
            $this->status = 'approved_level_2';
            $this->save();
        }

        return true;
    }

    public function reject($approverId, $notes = null)
    {
        $currentApproval = $this->approvals()->where('status', 'pending')->first();
        
        if (!$currentApproval) {
            return false;
        }

        $currentApproval->update([
            'status' => 'rejected',
            'approver_id' => $approverId,
            'notes' => $notes,
            'approved_at' => now()
        ]);

        $this->status = 'rejected';
        $this->save();

        return true;
    }
}
