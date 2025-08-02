<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderApproval;
use App\Models\Approver;
use App\Models\ApprovalLevel;
use App\Models\PrAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class POController extends Controller
{
    public function index()
    {
        $page = request()->query('page', 'dashboard');

        $views = [
            'dashboard' => 'approvals.po.dashboard',
            'search' => 'approvals.po.search',
            'pending' => 'approvals.po.pending',
        ];

        if ($page === 'dashboard') {
            // Get current user's approval levels
            $user = Auth::user();
            $userApproverLevels = Approver::where('user_id', $user->id)
                ->pluck('approval_level_id');

            // Get pending count - POs waiting for user's approval
            $pendingCount = PurchaseOrder::select('purchase_orders.id')
                ->join('purchase_order_approvals', 'purchase_orders.id', '=', 'purchase_order_approvals.purchase_order_id')
                ->whereIn('purchase_order_approvals.approval_level_id', $userApproverLevels)
                ->where('purchase_order_approvals.status', '=', 'pending')
                ->where('purchase_orders.status', '=', 'submitted')
                ->where('purchase_orders.status', '!=', 'revision')
                // Exclude POs that have been rejected or revision requested in any approval record
                ->whereNotIn('purchase_orders.id', function ($query) {
                    $query->select('purchase_order_id')
                        ->from('purchase_order_approvals')
                        ->whereIn('status', ['rejected', 'revision']);
                })
                ->distinct()
                ->count();

            // Get approved count - POs approved by this user
            $approvedCount = PurchaseOrderApproval::whereHas('approver', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('status', 'approved')
                ->count();

            // Get rejected count - POs rejected by this user
            $rejectedCount = PurchaseOrderApproval::whereHas('approver', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('status', 'rejected')
                ->count();

            // Get revision count - POs where revision was requested by this user
            $revisionCount = PurchaseOrderApproval::whereHas('approver', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('status', 'revision')
                ->count();

            // Get recent activity
            $recentActivity = PurchaseOrderApproval::whereHas('approver', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->with('purchase_order')
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();

            return view($views[$page], compact(
                'pendingCount',
                'approvedCount',
                'rejectedCount',
                'revisionCount',
                'recentActivity'
            ));
        }

        return view($views[$page]);
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['approvals', 'attachments', 'details']);

        // Filter out corrupt PO attachments without deleting them
        $validAttachments = $purchaseOrder->attachments->filter(function ($attachment) {
            // For development environment, don't check if file exists in storage
            // This allows us to display attachments even if files are only on production
            $isValid = is_numeric($attachment->file_size) &&
                !empty($attachment->file_path);

            if (!$isValid) {
                Log::info('Found corrupt PO attachment (skipping): ' . $attachment->id);
            }

            return $isValid;
        });

        // Replace the attachments collection with only valid ones
        $purchaseOrder->setRelation('attachments', $validAttachments);

        // Get PR attachments based on pr_no with validation (no deletion)
        $prAttachments = collect();

        if ($purchaseOrder->pr_no) {
            $prAttachments = PrAttachment::where('pr_no', $purchaseOrder->pr_no)
                ->whereNotNull('file_path')
                ->where('file_path', '!=', '')
                ->get()
                ->filter(function ($attachment) {
                    // For development environment, don't check if file exists in storage
                    // This allows us to display attachments even if files are only on production
                    $isValid = is_numeric($attachment->file_size) &&
                        $attachment->file_size > 0;

                    // Log corrupt attachments but don't delete due to foreign key constraints
                    if (!$isValid) {
                        Log::warning('Found corrupt PR attachment (skipping)', [
                            'attachment_id' => $attachment->id,
                            'original_name' => $attachment->original_name,
                            'file_path' => $attachment->file_path,
                            'reason' => !is_numeric($attachment->file_size) ? 'Invalid file size' : 'Zero file size'
                        ]);
                    }

                    return $isValid;
                });
        }

        return view('approvals.po.show', compact('purchaseOrder', 'prAttachments'));
    }

    public function getAttachments(PurchaseOrder $purchaseOrder)
    {
        return response()->json([
            'attachments' => $purchaseOrder->attachments
        ]);
    }

    /**
     * Show the pending approvals page
     */
    public function pending()
    {
        return view('approvals.po.pending');
    }

    public function search(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = PurchaseOrder::query()->orderBy('created_at', 'desc');

                // Apply filters
                if ($request->doc_num) {
                    $query->where('doc_num', 'like', '%' . $request->doc_num . '%');
                }

                if ($request->supplier_name) {
                    $query->where('supplier_name', 'like', '%' . $request->supplier_name . '%');
                }

                if ($request->date_from) {
                    $query->whereDate('doc_date', '>=', $request->date_from);
                }

                if ($request->date_to) {
                    $query->whereDate('doc_date', '<=', $request->date_to);
                }

                return datatables()
                    ->of($query)
                    ->addIndexColumn()
                    ->editColumn('doc_date', function ($po) {
                        return $po->doc_date->format('d M Y');
                    })
                    ->editColumn('create_date', function ($po) {
                        return $po->create_date ? $po->create_date->format('d M Y') : '-';
                    })
                    ->editColumn('status', function ($po) {
                        return '<span class="badge badge-' . ($po->status === 'draft' ? 'warning' : 'success') . '">'
                            . ucfirst($po->status) . '</span>';
                    })
                    ->addColumn('action', function ($model) {
                        return view('approvals.po.action', compact('model'))->render();
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }

            return view('approvals.po.search');
        } catch (\Exception $e) {
            Log::error('Error in search: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while searching'
            ], 500);
        }
    }

    /**
     * Get pending POs that require the logged-in user's approval
     */
    public function pendingData()
    {
        try {
            // Get the current user's approval levels
            $user = Auth::user();
            $userApproverLevels = Approver::where('user_id', $user->id)
                ->pluck('approval_level_id');

            if ($userApproverLevels->isEmpty()) {
                // If user has no approver levels, return empty result
                return datatables()
                    ->of([])
                    ->make(true);
            }

            // Build query to get POs that need the current user's approval
            $query = PurchaseOrder::select(
                'purchase_orders.*',
                'approval_levels.name as approval_level',
                'approval_levels.level as approval_level_number',
                'suppliers.name as supplier_name'
            )
                ->join('purchase_order_approvals', 'purchase_orders.id', '=', 'purchase_order_approvals.purchase_order_id')
                ->join('approval_levels', 'purchase_order_approvals.approval_level_id', '=', 'approval_levels.id')
                ->join('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
                ->whereIn('purchase_order_approvals.approval_level_id', $userApproverLevels)
                ->where('purchase_order_approvals.status', '=', 'pending')
                // Only show POs with 'submitted' status (not draft, approved, rejected, or revision)
                ->where('purchase_orders.status', '=', 'submitted')
                ->where('purchase_orders.status', '!=', 'revision')
                ->where('purchase_orders.status', '!=', 'revision')
                // Exclude POs that have been rejected or revision requested in any approval record
                ->whereNotIn('purchase_orders.id', function ($query) {
                    $query->select('purchase_order_id')
                        ->from('purchase_order_approvals')
                        ->whereIn('status', ['rejected', 'revision']);
                })
                ->with('purchaseOrderDetails')
                ->orderBy('purchase_order_approvals.created_at', 'desc');

            return datatables()
                ->of($query)
                ->addIndexColumn()
                ->editColumn('doc_date', function ($po) {
                    return $po->doc_date->format('d M Y');
                })
                ->editColumn('supplier_name', function ($po) {
                    return $po->supplier_name ?? '-';
                })
                ->addColumn('total_amount', function ($po) {
                    $total = $po->purchaseOrderDetails->sum(function ($detail) {
                        return $detail->qty * $detail->unit_price;
                    });
                    return number_format($total, 2);
                })
                ->addColumn('action', function ($po) {
                    return view('approvals.po.action-pending', compact('po'))->render();
                })
                ->filterColumn('doc_num', function ($query, $keyword) {
                    $query->where('purchase_orders.doc_num', 'like', "%{$keyword}%");
                })
                ->filterColumn('project_code', function ($query, $keyword) {
                    $query->where('purchase_orders.project_code', 'like', "%{$keyword}%");
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            Log::error('Error in pendingData: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while fetching data'
            ], 500);
        }
    }
}
