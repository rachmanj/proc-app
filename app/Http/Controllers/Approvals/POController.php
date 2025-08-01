<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PrAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class POController extends Controller
{
    public function index()
    {
        $page = request()->query('page', 'dashboard');
        
        // Get user's approval levels
        $userApprovalLevels = auth()->user()->approvers()->with('approvalLevel')->get();
        
        $purchaseOrders = PurchaseOrder::with(['supplier', 'approvals.approval_level'])
            ->where('status', 'submitted')
            ->whereHas('approvals', function($query) use ($userApprovalLevels) {
                $query->whereIn('approval_level_id', $userApprovalLevels->pluck('approval_level_id'))
                    ->where('status', 'pending');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $views = [
            'dashboard' => 'approvals.po.dashboard',
            'search' => 'approvals.po.search',
        ];

        return view($views[$page], compact('purchaseOrders'));
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['approvals', 'attachments', 'details']);
        
        // Auto-cleanup corrupt PO attachments
        $purchaseOrder->attachments->each(function($attachment) {
            if (!is_numeric($attachment->file_size) || 
                empty($attachment->file_path) || 
                !\Illuminate\Support\Facades\Storage::disk('public')->exists($attachment->file_path)) {
                \Log::info('Auto-deleting corrupt PO attachment: ' . $attachment->id);
                $attachment->delete();
            }
        });
        
        // Get PR attachments based on pr_no with validation and auto-cleanup
        $prAttachments = PrAttachment::where('pr_no', $purchaseOrder->pr_no)
            ->whereNotNull('file_path')
            ->where('file_path', '!=', '')
            ->get()
            ->filter(function($attachment) {
                // Additional validation: check if file size is numeric and file exists
                $isValid = is_numeric($attachment->file_size) && 
                          $attachment->file_size > 0 &&
                          \Illuminate\Support\Facades\Storage::disk('public')->exists($attachment->file_path);
                
                // Auto-delete corrupt attachments
                if (!$isValid) {
                    \Log::warning('Auto-deleting corrupt PR attachment', [
                        'attachment_id' => $attachment->id,
                        'original_name' => $attachment->original_name,
                        'file_size' => $attachment->file_size,
                        'file_path' => $attachment->file_path,
                        'reason' => !is_numeric($attachment->file_size) ? 'Invalid file size' : 
                                   ($attachment->file_size <= 0 ? 'Zero file size' : 'File not found')
                    ]);
                    $attachment->delete();
                }
                
                return $isValid;
            });

        return view('approvals.po.show', compact('purchaseOrder', 'prAttachments'));
    }

    public function getAttachments(PurchaseOrder $purchaseOrder)
    {
        return response()->json([
            'attachments' => $purchaseOrder->attachments
        ]);
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
}
