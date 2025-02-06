<?php

namespace App\Http\Controllers\Approvals;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class POController extends Controller
{
    public function index()
    {
        $page = request()->query('page', 'dashboard');

        $views = [
            'dashboard' => 'approvals.po.dashboard',
            'search' => 'approvals.po.search',
        ];

        return view($views[$page]);
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('approvals');
        $purchaseOrder->load('attachments');

        return view('approvals.po.show', compact('purchaseOrder'));
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
