<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\ApprovalLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrderApproval;
use App\Models\Approver;
use App\Notifications\PoPendingApprovalNotification;
use App\Notifications\ApprovalStatusChangedNotification;

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
            // Update status and submitted_by
            $purchaseOrder->status = 'submitted';
            $purchaseOrder->submitted_by = Auth::user() ? Auth::user()->name : 'System';
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

            $approvers = Approver::where('approval_level_id', $firstLevel->id)
                ->with('user')
                ->get();

            foreach ($approvers as $approver) {
                if ($approver->user) {
                    $approver->user->notify(new PoPendingApprovalNotification(
                        $purchaseOrder,
                        $firstLevel->name
                    ));
                }
            }

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

            $currentApproval = $purchaseOrder->approvals()
                ->where('status', 'pending')
                ->with('approval_level')
                ->first();

            $purchaseOrder->approve(Auth::id(), $request->notes);

            $purchaseOrder->refresh();
            $approver = Auth::user();

            if ($purchaseOrder->status === 'approved') {
                $creator = \App\Models\User::where('name', $purchaseOrder->submitted_by)->first();
                if ($creator) {
                    $creator->notify(new ApprovalStatusChangedNotification(
                        $purchaseOrder,
                        'approved',
                        $approver->name
                    ));
                }
            } else {
                $nextApproval = $purchaseOrder->approvals()
                    ->where('status', 'pending')
                    ->with('approval_level')
                    ->first();

                if ($nextApproval) {
                    $nextApprovers = Approver::where('approval_level_id', $nextApproval->approval_level_id)
                        ->with('user')
                        ->get();

                    foreach ($nextApprovers as $nextApprover) {
                        if ($nextApprover->user) {
                            $nextApprover->user->notify(new PoPendingApprovalNotification(
                                $purchaseOrder,
                                $nextApproval->approval_level->name
                            ));
                        }
                    }
                }
            }

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
            // Begin transaction to ensure all updates happen together
            DB::beginTransaction();

            // Get the current user's approval levels
            $user = Auth::user();
            $userApproverLevels = Approver::where('user_id', $user->id)
                ->pluck('approval_level_id');

            // Get the current pending approval for this PO
            $currentApproval = $purchaseOrder->approvals()
                ->where('status', 'pending')
                ->whereIn('approval_level_id', $userApproverLevels)
                ->first();

            if (!$currentApproval) {
                throw new \Exception('No pending approval found for your approval level');
            }

            // Now call the reject method with the current user ID
            $purchaseOrder->reject(Auth::id(), $request->notes);
            $purchaseOrder->refresh();

            $approver = Auth::user();
            $creator = \App\Models\User::where('name', $purchaseOrder->submitted_by)->first();
            if ($creator) {
                $creator->notify(new ApprovalStatusChangedNotification(
                    $purchaseOrder,
                    'rejected',
                    $approver->name
                ));
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order has been rejected'
            ]);
        } catch (\Exception $e) {
            // Rollback transaction on error
            if (isset($DB) && DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            Log::error('Error in rejection request: ' . $e->getMessage(), [
                'po_id' => $purchaseOrder->id,
                'user_id' => Auth::id()
            ]);

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
            // Begin transaction to ensure all updates happen together
            DB::beginTransaction();

            // Get the current user's approval levels
            $user = Auth::user();
            $userApproverLevels = Approver::where('user_id', $user->id)
                ->pluck('approval_level_id');

            // Get the current pending approval for this PO
            $currentApproval = $purchaseOrder->approvals()
                ->where('status', 'pending')
                ->whereIn('approval_level_id', $userApproverLevels)
                ->first();

            if (!$currentApproval) {
                throw new \Exception('No pending approval found for your approval level');
            }

            // Use the model's revise method which handles all the logic
            $purchaseOrder->revise(Auth::id(), $request->notes);
            $purchaseOrder->refresh();

            $approver = Auth::user();
            $creator = \App\Models\User::where('name', $purchaseOrder->submitted_by)->first();
            if ($creator) {
                $creator->notify(new ApprovalStatusChangedNotification(
                    $purchaseOrder,
                    'revision',
                    $approver->name
                ));
            }

            // Commit the transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Revision has been requested'
            ]);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            Log::error('Error in revision request: ' . $e->getMessage(), [
                'po_id' => $purchaseOrder->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error requesting revision: ' . $e->getMessage()
            ], 500);
        }
    }
}
