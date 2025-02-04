<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\ApprovalLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function approve(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'notes' => 'nullable|string'
        ]);

        $purchaseOrder->approve(auth()->user()->id, $request->notes);
        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order approved');
    }

    public function reject(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'notes' => 'nullable|string'
        ]);

        $purchaseOrder->reject(auth()->user()->id, $request->notes);
        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order rejected');
    }
} 