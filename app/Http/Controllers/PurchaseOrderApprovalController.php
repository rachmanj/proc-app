<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\ApprovalLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PurchaseOrderApproval;
use App\Models\Approver;

class PurchaseOrderApprovalController extends Controller
{
    public function submit(PurchaseOrder $purchaseOrder)
    {
        Log::info('Submit method called with data:', [
            'purchase_order_id' => $purchaseOrder->id,
            'current_status' => $purchaseOrder->status,
            'request_method' => request()->method(),
            'request_all' => request()->all()
        ]);

        if ($purchaseOrder->status !== 'draft') {
            Log::warning('PO not in draft status: ' . $purchaseOrder->status);
            return response()->json([
                'success' => false,
                'message' => 'This purchase order cannot be submitted'
            ], 422);
        }

        try {
            // Update status
            $purchaseOrder->status = 'submitted';
            $purchaseOrder->save();
            Log::info('PO status updated to submitted');


            // Create first level approval record
            $firstLevel = ApprovalLevel::where('level', 1)->first();
            if (!$firstLevel) {
                Log::error('No level 1 approval found');
                throw new \Exception('Approval level 1 not found');
            }


            $purchaseOrder->approvals()->create([
                'approval_level_id' => $firstLevel->id,
                'status' => 'pending',
                'approver_id' => null
            ]);
            Log::info('Approval record created');


            return response()->json([
                'success' => true,
                'message' => 'Purchase Order has been submitted for approval'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in submit: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error submitting purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, PurchaseOrderApproval $purchaseOrderApproval)
    {
        $request->validate([
            'notes' => 'nullable|string'
        ]);

        try {
            $purchaseOrder = $purchaseOrderApproval->purchaseOrder;
            if (!$purchaseOrder) {
                throw new \Exception('Purchase Order not found');
            }

            $purchaseOrder->approve(auth()->user()->id, $request->notes);
            
            return response()->json([
                'success' => true,
                'message' => 'Purchase Order has been approved'
            ]);
        } catch (\Exception $e) {
            Log::error('Approval error:', [
                'approval_id' => $purchaseOrderApproval->id,
                'po_id' => $purchaseOrderApproval->purchase_order_id,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error approving purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'notes' => 'nullable|string'
        ]);

        try {
            $purchaseOrder->reject(auth()->user()->id, $request->notes);
            
            return response()->json([
                'success' => true,
                'message' => 'Purchase Order has been rejected'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function revise(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'notes' => 'required|string'
        ]);

        try {
            $purchaseOrder->status = PurchaseOrder::STATUS_REVISION;
            $purchaseOrder->save();

            // Add revision note to approvals
            $currentApproval = $purchaseOrder->approvals()
                ->where('status', 'pending')
                ->first();

            if ($currentApproval) {
                $approver = Approver::where('user_id', auth()->id())
                    ->where('approval_level_id', $currentApproval->approval_level_id)
                    ->first();

                if (!$approver) {
                    throw new \Exception('User is not authorized for this action');
                }

                $currentApproval->update([
                    'status' => 'revision_requested',
                    'notes' => $request->notes,
                    'approver_id' => $approver->id
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Revision has been requested'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error requesting revision: ' . $e->getMessage()
            ], 500);
        }
    }
} 