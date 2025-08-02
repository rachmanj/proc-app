<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;
use App\Models\Approver;

class PurchaseOrder extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REVISION = 'revision';

    protected $attributes = [
        'status' => self::STATUS_DRAFT
    ];

    protected $fillable = [
        'doc_num',
        'doc_date',
        'create_date',
        'day',
        'po_delivery_date',
        'supplier_id',
        'po_eta',
        'pr_no',
        'unit_no',
        'po_currency',
        'total_po_price',
        'po_with_vat',
        'project_code',
        'dept_code',
        'po_status',
        'po_delivery_status',
        'budget_type',
        'status',
        'submitted_by'
    ];

    protected $casts = [
        'doc_date' => 'date',
        'create_date' => 'date',
    ];

    protected $appends = ['day_difference'];

    public function getDayDifferenceAttribute()
    {
        // If status is approved, stop calculating and return stored day value
        if ($this->status === self::STATUS_APPROVED) {
            return $this->day; // Return the stored day value from database
        }

        if (!$this->create_date) {
            return null;
        }

        $createDate = \Carbon\Carbon::parse($this->create_date);
        $today = \Carbon\Carbon::today();

        return $createDate->diffInDays($today);
    }

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
        $this->status = self::STATUS_SUBMITTED;
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
        try {
            // Find the current pending approval
            $currentApproval = $this->approvals()
                ->where('status', 'pending')
                ->with('approval_level')
                ->first();

            // Get the approver record for this user and level
            $approver = Approver::where('user_id', $approverId)
                ->where('approval_level_id', $currentApproval->approval_level_id)
                ->first();

            if (!$approver) {
                throw new \Exception('User is not authorized to approve this level');
            }

            Log::info('Approving PO:', [
                'po_id' => $this->id,
                'current_approval' => $currentApproval ? [
                    'id' => $currentApproval->id,
                    'level' => $currentApproval->approval_level->level ?? null,
                    'status' => $currentApproval->status
                ] : null
            ]);

            if (!$currentApproval) {
                throw new \Exception('No pending approval found');
            }

            // Update the current approval
            $currentApproval->update([
                'status' => 'approved',
                'approver_id' => $approver->id,
                'notes' => $notes,
                'approved_at' => now()
            ]);

            // Get the next approval level
            $nextLevel = ApprovalLevel::where('level', '>', $currentApproval->approval_level->level)
                ->orderBy('level')
                ->first();

            Log::info('Next approval level:', [
                'current_level' => $currentApproval->approval_level->level,
                'next_level' => $nextLevel ? $nextLevel->level : null
            ]);

            if ($nextLevel) {
                // Create next level approval
                $this->approvals()->create([
                    'approval_level_id' => $nextLevel->id,
                    'status' => 'pending'
                ]);
            } else {
                // If no next level, mark PO as approved
                $this->status = self::STATUS_APPROVED;
                $this->save();
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error in PO approval: ' . $e->getMessage(), [
                'purchase_order_id' => $this->id,
                'approver_id' => $approverId,
                'current_approval' => $currentApproval ?? null
            ]);
            throw $e;
        }
    }

    public function reject($approverId, $notes = null)
    {
        $currentApproval = $this->approvals()->where('status', 'pending')->first();

        if (!$currentApproval) {
            return false;
        }

        // Get the approver record for this user and level
        $approver = Approver::where('user_id', $approverId)
            ->where('approval_level_id', $currentApproval->approval_level_id)
            ->first();

        if (!$approver) {
            throw new \Exception('User is not authorized to reject this level');
        }

        $currentApproval->update([
            'status' => 'rejected',
            'approver_id' => $approver->id,
            'notes' => $notes,
            'approved_at' => now()
        ]);

        $this->status = self::STATUS_REJECTED;
        $this->save();

        return true;
    }

    public function revise($approverId, $notes = null)
    {
        $currentApproval = $this->approvals()->where('status', 'pending')->first();

        if (!$currentApproval) {
            return false;
        }

        // Get the approver record for this user and level
        $approver = Approver::where('user_id', $approverId)
            ->where('approval_level_id', $currentApproval->approval_level_id)
            ->first();

        if (!$approver) {
            throw new \Exception('User is not authorized to request revision for this level');
        }

        $currentApproval->update([
            'status' => 'revision', // Using 'revision' instead of 'revision_requested' to match the ENUM
            'approver_id' => $approver->id,
            'notes' => $notes,
            'approved_at' => now()
        ]);

        // Cancel any other pending approvals for this PO
        $this->approvals()
            ->where('id', '!=', $currentApproval->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        $this->status = self::STATUS_REVISION;
        $this->save();

        return true;
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrderDetails()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    // Alias for purchaseOrderDetails for convenience
    public function details()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }
}
