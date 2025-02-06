<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PoAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class POController extends Controller
{
    public function index()
    {
        $page = request()->query('page', 'dashboard');

        $views = [
            'dashboard' => 'procurement.po.dashboard',
            'create' => 'procurement.po.create',
            'list' => 'procurement.po.list',
            'search' => 'procurement.po.search',
        ];

        return view($views[$page]);
    }

    /**
     * Upload attachments for a purchase order
     */
    public function attachFiles(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'attachments.*' => 'required|file|max:5120', // 5MB max per file
            'descriptions.*' => 'nullable|string|max:255',
        ]);

        $attachments = $purchaseOrder->attachFiles(
            $request->file('attachments'),
            $request->input('descriptions', [])
        );

        return response()->json([
            'message' => 'Files uploaded successfully',
            'attachments' => $attachments
        ]);
    }

    /**
     * Remove an attachment from a purchase order
     */
    public function detachFile(Request $request, $attachmentId)
    {
        try {
            $attachment = PoAttachment::findOrFail($attachmentId);

            // Delete the physical file
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            // Detach from all purchase orders and delete the attachment record
            $attachment->purchaseOrders()->detach();
            $attachment->delete();

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting attachment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attachment'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doc_num' => 'required|string|max:30|unique:purchase_orders',
            'doc_date' => 'required|date',
            'create_date' => 'nullable|date',
            'supplier_name' => 'required|string|max:255',
        ]);

        PurchaseOrder::create($validated);

        return response()->json([
            'message' => 'Purchase Order created successfully!',
            'redirect' => route('procurement.po.index', ['page' => 'create'])
        ]);
    }

    public function data()
    {
        $query = PurchaseOrder::query()->orderBy('created_at', 'desc');

        return datatables()->of($query)
            ->editColumn('doc_date', function ($po) {
                return $po->doc_date->format('d M Y');
            })
            ->editColumn('create_date', function ($po) {
                return $po->create_date ? $po->create_date->format('d M Y') : '-';
            })
            ->addColumn('action', function ($model) {
                return view('procurement.po.action', compact('model'))->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        try {
            $purchaseOrder->delete();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Purchase Order.'
            ], 500);
        }
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        return view('procurement.po.edit', compact('purchaseOrder'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'doc_num' => 'required|string|max:30|unique:purchase_orders,doc_num,' . $purchaseOrder->id,
            'doc_date' => 'required|date',
            'create_date' => 'nullable|date',
            'supplier_name' => 'required|string|max:255',
        ]);

        $purchaseOrder->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order updated successfully!'
        ]);
    }

    public function getAttachments(PurchaseOrder $purchaseOrder)
    {
        return response()->json([
            'attachments' => $purchaseOrder->attachments
        ]);
    }

    public function uploadAttachments(Request $request, PurchaseOrder $purchaseOrder)
    {
        try {
            $request->validate([
                'attachments.*' => 'required|file|max:5120', // 5MB max per file
            ]);

            if (!$request->hasFile('attachments')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No files were uploaded.'
                ], 422);
            }

            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('po-attachments', 'public');

                $attachment = PoAttachment::create([
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                ]);

                $purchaseOrder->attachments()->attach($attachment->id);
                $attachments[] = $attachment;
            }

            return response()->json([
                'success' => true,
                'message' => 'Attachments uploaded successfully',
                'attachments' => $attachments
            ]);
        } catch (\Exception $e) {
            Log::error('Error uploading attachments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error uploading attachments: ' . $e->getMessage()
            ], 500);
        }
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
                        return '<span class="badge badge-' . ($po->status === 'draft' ? 'warning' : ($po->status === 'submitted' ? 'info' : ($po->status === 'approved' ? 'success' : ($po->status === 'rejected' ? 'danger' : '')))) . '">'
                            . ucfirst($po->status) . '</span>';
                    })
                    ->addColumn('action', function ($model) {
                        return view('procurement.po.action', compact('model'))->render();
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }

            return view('procurement.po.search');
        } catch (\Exception $e) {
            Log::error('Error in search: ' . $e->getMessage());
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while searching'
            ], 500);
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        return view('procurement.po.show', compact('purchaseOrder'));
    }
}
