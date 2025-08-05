<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PoAttachment;
use App\Models\Supplier;
use App\Models\PrAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;

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

        if ($page == 'search') {
            $suppliers = Supplier::orderBy('name')->pluck('name');
            $unitNos = PurchaseOrder::distinct()->orderBy('unit_no')->pluck('unit_no');
            $projectCodes = PurchaseOrder::distinct()->orderBy('project_code')->pluck('project_code');
            $statuses = PurchaseOrder::distinct()->orderBy('status')->pluck('status');

            return view($views[$page], compact('suppliers', 'unitNos', 'projectCodes', 'statuses'));
        } 

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
        $query = PurchaseOrder::query()
            ->with(['supplier', 'approvals'])
            ->selectRaw('purchase_orders.*, 
                CASE 
                    WHEN status = "approved" THEN COALESCE(day, 0)
                    ELSE DATEDIFF(CURDATE(), create_date) 
                END as calculated_day')
            ->orderByRaw('calculated_day DESC, created_at DESC');

        return datatables()->of($query)
            ->addColumn('day', function ($po) {
                // Use calculated_day from raw query or day_difference
                return $po->calculated_day ?? $po->day_difference;
            })
            ->editColumn('doc_date', function ($po) {
                return $po->doc_date->format('d M Y');
            })
            ->editColumn('create_date', function ($po) {
                return $po->create_date ? $po->create_date->format('d M Y') : '-';
            })
            ->addColumn('supplier_name', function ($po) {
                return $po->supplier ? $po->supplier->name : '-';
            })
            ->editColumn('status', function ($po) {
                $approvalLevel1 = $po->approvals->where('level', 1)->first();
                $approvalLevel2 = $po->approvals->where('level', 2)->first();
                
                // Status dari tabel purchase_orders
                $poStatusClass = match($po->status) {
                    'draft' => 'warning',
                    'submitted' => 'info',
                    'approved_level_1' => 'success',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    'revision' => 'secondary',
                    default => 'secondary'
                };

                $statusHtml = '<div class="d-flex flex-column">';
                $statusHtml .= '<span class="badge badge-' . $poStatusClass . ' mb-1">PO: ' . ucfirst($po->status) . '</span>';

                // Status approval level 1
                if ($approvalLevel1) {
                    $level1Class = match($approvalLevel1->status) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'secondary'
                    };
                    $statusHtml .= '<span class="badge badge-' . $level1Class . ' mb-1">Level 1: ' . ucfirst($approvalLevel1->status) . '</span>';
                }

                // Status approval level 2
                if ($approvalLevel2) {
                    $level2Class = match($approvalLevel2->status) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'pending' => 'warning',
                        default => 'secondary'
                    };
                    $statusHtml .= '<span class="badge badge-' . $level2Class . '">Level 2: ' . ucfirst($approvalLevel2->status) . '</span>';
                }

                $statusHtml .= '</div>';
                return $statusHtml;
            })
            ->addColumn('action', function ($model) {
                return view('procurement.po.action', compact('model'))->render();
            })
            ->rawColumns(['status', 'action'])
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
        // Load the purchase order details relationship
        $purchaseOrder->load('details');
        
        $suppliers = Supplier::orderBy('name')->get();
        $prAttachments = PrAttachment::where('pr_no', $purchaseOrder->pr_no)->get();
        
        return view('procurement.po.edit', compact('purchaseOrder', 'suppliers', 'prAttachments'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {

       
        $validated = $request->validate([
            'doc_num' => 'required|string|max:30|unique:purchase_orders,doc_num,' . $purchaseOrder->id,
            'doc_date' => 'required|date',
            'create_date' => 'nullable|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'project_code' => 'nullable|string|max:50',
            'unit_no' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.item_code' => 'required|string|max:50',
            'items.*.description' => 'required|string',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.uom' => 'required|string|max:20',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function() use ($purchaseOrder, $validated) {
                // Update PO header
                $purchaseOrder->update([
                    'doc_num' => $validated['doc_num'],
                    'doc_date' => $validated['doc_date'],
                    'create_date' => $validated['create_date'],
                    'supplier_id' => $validated['supplier_id'],
                    'project_code' => $validated['project_code'],
                    'unit_no' => $validated['unit_no'],
                ]);

                // Update PO details
                $purchaseOrder->purchaseOrderDetails()->delete(); // Remove existing items
                foreach ($validated['items'] as $item) {
                    $purchaseOrder->purchaseOrderDetails()->create([
                        'item_code' => $item['item_code'],
                        'description' => $item['description'],
                        'remark1' => $item['remark1'] ?? null,
                        'remark2' => $item['remark2'] ?? null,
                        'qty' => $item['qty'],
                        'uom' => $item['uom'],
                        'unit_price' => $item['unit_price'],
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating purchase order: ' . $e->getMessage()
            ], 500);
        }
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
                'attachments' => 'required|array|min:1',
                'attachments.*' => 'required|file|max:10240', // 10MB max per file
                'descriptions' => 'array',
                'descriptions.*' => 'nullable|string|max:255'
            ]);

            $attachments = [];
            
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');
                $descriptions = $request->input('descriptions', []);
                
                foreach ($files as $index => $file) {
                    // Store file
                    $path = $file->store('po_attachments', 'public');
                    
                    // Create attachment record
                    $attachment = PoAttachment::create([
                        'original_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'description' => $descriptions[$index] ?? null
                    ]);
                    
                    // Attach to purchase order via pivot table
                    $purchaseOrder->attachments()->attach($attachment->id);
                    
                    $attachments[] = $attachment;
                }
            }

            // Set flash message
            return redirect()->back()->with('success', 'Attachments uploaded successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->with('error', 'Validation errors: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            Log::error('Error uploading attachments: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error uploading attachments: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            if ($request->ajax()) {
                $query = PurchaseOrder::with('supplier')->orderBy('created_at', 'desc');

                // Apply filters
                if ($request->doc_num) {
                    $query->where('doc_num', 'like', '%' . $request->doc_num . '%');
                }

                if ($request->pr_num) {
                    $query->where('pr_num', 'like', '%' . $request->pr_num . '%');
                }

                if ($request->supplier_name) {
                    $query->whereHas('supplier', function($q) use ($request) {
                        $q->where('name', 'like', '%' . $request->supplier_name . '%');
                    });
                }

                if ($request->unit_no) {
                    $query->where('unit_no', $request->unit_no);
                }

                if ($request->project_code) {
                    $query->where('project_code', $request->project_code);
                }

                if ($request->status) {
                    $query->where('status', $request->status);
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
                    ->editColumn('supplier_name', function ($po) {
                        return $po->supplier->name ?? '-';
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

            // Get distinct values for dropdowns
            $suppliers = \App\Models\Supplier::orderBy('name')->pluck('name');
            $unitNos = PurchaseOrder::distinct()->orderBy('unit_no')->pluck('unit_no');
            $projectCodes = PurchaseOrder::distinct()->orderBy('project_code')->pluck('project_code');
            $statuses = PurchaseOrder::distinct()->orderBy('status')->pluck('status');

            return view('procurement.po.search', compact('suppliers', 'unitNos', 'projectCodes', 'statuses'));
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
        // Load the purchase order details relationship
        $purchaseOrder->load('details');
        
        // Get PR attachments based on pr_no with validation
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
        
        return view('procurement.po.show', compact('purchaseOrder', 'prAttachments'));
    }

    public function updateAttachment(Request $request, $attachmentId)
    {
        try {
            $request->validate([
                'description' => 'nullable|string|max:255',
                'file' => 'nullable|file|max:10240' // 10MB max file size
            ]);

            $attachment = PoAttachment::findOrFail($attachmentId);
            
            // Update description
            $attachment->update([
                'description' => $request->input('description')
            ]);

            // Handle file upload if a new file is provided
            if ($request->hasFile('file')) {
                // Delete old file from storage
                if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }

                // Store new file
                $file = $request->file('file');
                $path = $file->store('po_attachments', 'public');
                
                $attachment->update([
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Attachment updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating attachment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating attachment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function viewAttachment($attachmentId)
    {
        try {
            $attachment = PoAttachment::findOrFail($attachmentId);
            
            // Validate attachment data first
            if (!is_numeric($attachment->file_size) || empty($attachment->file_path)) {
                // Log the corrupt attachment
                \Log::warning('Corrupt PO attachment detected and deleted', [
                    'attachment_id' => $attachment->id,
                    'original_name' => $attachment->original_name,
                    'file_size' => $attachment->file_size,
                    'file_path' => $attachment->file_path
                ]);
                
                // Delete corrupt attachment
                $attachment->delete();
                
                $errorHtml = '<!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Invalid Attachment</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fa; }
                        .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; margin: 20px auto; max-width: 600px; }
                        .error h3 { margin-top: 0; color: #721c24; }
                        .error p { margin-bottom: 15px; line-height: 1.5; }
                        .btn { display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; font-weight: 500; }
                        .btn:hover { background-color: #0056b3; text-decoration: none; color: white; }
                        .icon { font-size: 48px; margin-bottom: 20px; }
                    </style>
                </head>
                <body>
                    <div class="error">
                        <div class="icon">⚠️</div>
                        <h3>Invalid Attachment</h3>
                        <p><strong>Attachment ID:</strong> ' . $attachment->id . '</p>
                        <p>The requested attachment had corrupt data and has been automatically removed from the system.</p>
                        <p>This attachment contained invalid information and could not be displayed safely.</p>
                        <a href="javascript:history.back()" class="btn">← Go Back</a>
                    </div>
                </body>
                </html>';
                
                return response($errorHtml, 400, ['Content-Type' => 'text/html']);
            }
            
            // Check if file exists in storage
            if (!Storage::disk('public')->exists($attachment->file_path)) {
                // Return a user-friendly error page for missing file
                $errorHtml = '<!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>File Not Found</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fa; }
                        .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; margin: 20px auto; max-width: 600px; }
                        .error h3 { margin-top: 0; color: #721c24; }
                        .error p { margin-bottom: 15px; line-height: 1.5; }
                        .btn { display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; font-weight: 500; }
                        .btn:hover { background-color: #0056b3; text-decoration: none; color: white; }
                        .icon { font-size: 48px; margin-bottom: 20px; }
                    </style>
                </head>
                <body>
                    <div class="error">
                        <div class="icon">📄</div>
                        <h3>File Not Found</h3>
                        <p><strong>Attachment ID:</strong> ' . $attachment->id . '</p>
                        <p><strong>File Path:</strong> ' . $attachment->file_path . '</p>
                        <p>The requested file could not be found in the system. This might be because:</p>
                        <ul>
                            <li>The file has been deleted</li>
                            <li>The attachment ID is incorrect</li>
                            <li>The file was moved to a different location</li>
                        </ul>
                        <p>Please contact your administrator if you believe this is an error.</p>
                        <a href="javascript:history.back()" class="btn">← Go Back</a>
                    </div>
                </body>
                </html>';
                
                return response($errorHtml, 404, ['Content-Type' => 'text/html']);
            }

            // Get file mime type
            $mimeType = Storage::disk('public')->mimeType($attachment->file_path);
            
            // Read file content
            $fileContent = Storage::disk('public')->get($attachment->file_path);
            
            // Special headers for Chrome compatibility
            $headers = [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $attachment->original_name . '"',
                'Content-Length' => strlen($fileContent),
                'Cache-Control' => 'public, max-age=3600',
                'X-Content-Type-Options' => 'nosniff',
                'X-Frame-Options' => 'SAMEORIGIN',
                'Accept-Ranges' => 'bytes'
            ];
            
            // For PDF files, add specific headers that work better with Chrome
            if ($mimeType === 'application/pdf') {
                $headers['Content-Disposition'] = 'inline; filename="' . $attachment->original_name . '"';
                $headers['Content-Transfer-Encoding'] = 'binary';
            }
            
            // For Excel files, we'll handle them specially in the frontend
            // The backend will still serve them as downloadable files
            // but frontend will use Google Docs Viewer for preview
            if (in_array($mimeType, [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                'application/vnd.ms-excel', // .xls
                'application/vnd.ms-excel.sheet.macroEnabled.12', // .xlsm
                'application/vnd.ms-excel.template.macroEnabled.12' // .xltm
            ])) {
                // Keep inline for preview, but frontend will handle the preview logic
                $headers['Content-Disposition'] = 'inline; filename="' . $attachment->original_name . '"';
            }
            
            return response($fileContent, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error viewing attachment: ' . $e->getMessage(), [
                'attachment_id' => $attachmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a user-friendly error page
            $errorHtml = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Error Viewing File</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fa; }
                    .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; margin: 20px auto; max-width: 600px; }
                    .error h3 { margin-top: 0; color: #721c24; }
                    .error p { margin-bottom: 15px; line-height: 1.5; }
                    .btn { display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; font-weight: 500; }
                    .btn:hover { background-color: #0056b3; text-decoration: none; color: white; }
                    .icon { font-size: 48px; margin-bottom: 20px; }
                </style>
            </head>
            <body>
                <div class="error">
                    <div class="icon">⚠️</div>
                    <h3>Error Viewing File</h3>
                    <p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
                    <p>An error occurred while trying to view the file. This might be because:</p>
                    <ul>
                        <li>The file is corrupted</li>
                        <li>There is a system error</li>
                        <li>The file format is not supported</li>
                    </ul>
                    <p>Please contact your administrator if you believe this is an error.</p>
                    <a href="javascript:history.back()" class="btn">← Go Back</a>
                </div>
            </body>
            </html>';
            
            return response($errorHtml, 500, ['Content-Type' => 'text/html']);
        }
    }

    public function previewExcel($attachmentId)
    {
        try {
            // First, try to find the attachment
            $attachment = PoAttachment::find($attachmentId);
            
            if (!$attachment) {
                // Return a user-friendly error page for missing attachment
                $errorHtml = '<!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Excel Preview Error</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fa; }
                        .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; margin: 20px auto; max-width: 600px; }
                        .error h3 { margin-top: 0; color: #721c24; }
                        .error p { margin-bottom: 15px; line-height: 1.5; }
                        .btn { display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; font-weight: 500; }
                        .btn:hover { background-color: #0056b3; text-decoration: none; color: white; }
                        .icon { font-size: 48px; margin-bottom: 20px; }
                    </style>
                </head>
                <body>
                    <div class="error">
                        <div class="icon">📄</div>
                        <h3>File Not Found</h3>
                        <p><strong>Attachment ID:</strong> ' . $attachmentId . '</p>
                        <p>The requested file could not be found in the system. This might be because:</p>
                        <ul>
                            <li>The file has been deleted</li>
                            <li>The attachment ID is incorrect</li>
                            <li>The file was moved to a different location</li>
                        </ul>
                        <p>Please contact your administrator if you believe this is an error.</p>
                        <a href="javascript:history.back()" class="btn">← Go Back</a>
                    </div>
                </body>
                </html>';
                
                return response($errorHtml, 200, [
                    'Content-Type' => 'text/html'
                ]);
            }
            
            // Check if file exists in storage
            if (!Storage::disk('public')->exists($attachment->file_path)) {
                $errorHtml = '<!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Excel Preview Error</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fa; }
                        .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; margin: 20px auto; max-width: 600px; }
                        .error h3 { margin-top: 0; color: #721c24; }
                        .error p { margin-bottom: 15px; line-height: 1.5; }
                        .btn { display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; font-weight: 500; }
                        .btn:hover { background-color: #0056b3; text-decoration: none; color: white; }
                        .icon { font-size: 48px; margin-bottom: 20px; }
                    </style>
                </head>
                <body>
                    <div class="error">
                        <div class="icon">📁</div>
                        <h3>File Not Found in Storage</h3>
                        <p><strong>File:</strong> ' . $attachment->original_name . '</p>
                        <p><strong>Path:</strong> ' . $attachment->file_path . '</p>
                        <p>The file exists in the database but the physical file is missing from storage.</p>
                        <p>Please contact your administrator to restore the file.</p>
                        <a href="javascript:history.back()" class="btn">← Go Back</a>
                    </div>
                </body>
                </html>';
                
                return response($errorHtml, 200, [
                    'Content-Type' => 'text/html'
                ]);
            }

            // Get file path
            $filePath = Storage::disk('public')->path($attachment->file_path);
            
            // Load the Excel file
            $spreadsheet = IOFactory::load($filePath);
            
            // Get the first worksheet
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Convert to HTML using the correct method
            $writer = new Html($spreadsheet);
            
            // Method 1: Try using generateHtmlAll() method
            if (method_exists($writer, 'generateHtmlAll')) {
                $html = $writer->generateHtmlAll();
            } else {
                // Method 2: Using output buffering
                ob_start();
                $writer->save('php://output');
                $html = ob_get_clean();
                
                // Method 3: If still empty, try temporary file
                if (empty($html)) {
                    $tempFile = tempnam(sys_get_temp_dir(), 'excel_preview_');
                    $writer->save($tempFile);
                    $html = file_get_contents($tempFile);
                    unlink($tempFile);
                }
            }
            
            // Add custom CSS for better styling
            $customCSS = '
            <style>
                body { 
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; 
                    margin: 20px; 
                    background-color: #f8f9fa;
                }
                
                .excel-preview { 
                    max-width: 100%; 
                    overflow-x: auto; 
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    padding: 20px;
                }
                
                .excel-preview h3 {
                    color: #333;
                    margin-bottom: 20px;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #007bff;
                }
                
                .excel-preview table { 
                    min-width: 100%; 
                    border-collapse: collapse;
                    font-size: 11px;
                    line-height: 1.4;
                }
                
                .excel-preview th, .excel-preview td { 
                    border: 1px solid #e0e0e0; 
                    padding: 8px 6px; 
                    text-align: left; 
                    font-size: 11px;
                    vertical-align: middle;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    max-width: 150px;
                }
                
                .excel-preview th { 
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    font-weight: 600;
                    font-size: 11px;
                    position: sticky; 
                    top: 0; 
                    z-index: 10;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }
                
                .excel-preview tr:nth-child(even) { 
                    background-color: #f8f9fa; 
                }
                
                .excel-preview tr:nth-child(odd) { 
                    background-color: #ffffff; 
                }
                
                .excel-preview tr:hover { 
                    background-color: #e3f2fd; 
                    transition: background-color 0.2s ease;
                }
                
                /* Number alignment for currency and numeric columns */
                .excel-preview td[data-type="number"],
                .excel-preview td[data-type="currency"] {
                    text-align: right;
                    font-family: "Courier New", monospace;
                    font-weight: 500;
                }
                
                /* Currency formatting */
                .excel-preview td[data-type="currency"] {
                    color: #2e7d32;
                    font-weight: 600;
                }
                
                /* Date alignment */
                .excel-preview td[data-type="date"] {
                    text-align: center;
                    color: #666;
                }
                
                /* Text alignment */
                .excel-preview td[data-type="text"] {
                    text-align: left;
                    color: #333;
                }
                
                /* Compact mode for many columns */
                .excel-preview.compact th,
                .excel-preview.compact td {
                    padding: 4px 3px;
                    font-size: 10px;
                }
                
                /* Responsive design */
                @media (max-width: 768px) {
                    .excel-preview {
                        padding: 10px;
                    }
                    .excel-preview th,
                    .excel-preview td {
                        padding: 4px 2px;
                        font-size: 10px;
                    }
                }
                
                /* Scrollbar styling */
                .excel-preview::-webkit-scrollbar {
                    height: 8px;
                }
                
                .excel-preview::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 4px;
                }
                
                .excel-preview::-webkit-scrollbar-thumb {
                    background: #c1c1c1;
                    border-radius: 4px;
                }
                
                .excel-preview::-webkit-scrollbar-thumb:hover {
                    background: #a8a8a8;
                }
            </style>
            ';
            
            // Combine HTML with custom CSS and JavaScript
            $fullHtml = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Excel Preview - ' . $attachment->original_name . '</title>
                ' . $customCSS . '
            </head>
            <body>
                <div class="excel-preview">
                    <h3>Excel Preview: ' . $attachment->original_name . '</h3>
                    ' . $html . '
                </div>
                
                <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // Auto-detect column types and apply proper alignment
                    const tables = document.querySelectorAll(".excel-preview table");
                    
                    tables.forEach(function(table) {
                        const rows = table.querySelectorAll("tr");
                        if (rows.length === 0) return;
                        
                        const headerRow = rows[0];
                        const headerCells = headerRow.querySelectorAll("th");
                        
                        // Process each data row
                        for (let i = 1; i < rows.length; i++) {
                            const cells = rows[i].querySelectorAll("td");
                            
                            cells.forEach(function(cell, index) {
                                if (index >= headerCells.length) return;
                                
                                const headerText = headerCells[index].textContent.toLowerCase();
                                const cellText = cell.textContent.trim();
                                
                                // Detect column type based on header and content
                                let dataType = "text";
                                
                                // Currency detection
                                if (headerText.includes("premi") || 
                                    headerText.includes("harga") || 
                                    headerText.includes("total") ||
                                    headerText.includes("fki") ||
                                    headerText.includes("fsi") ||
                                    headerText.includes("sp") ||
                                    cellText.includes("Rp.") ||
                                    cellText.includes("$") ||
                                    cellText.includes(",")) {
                                    dataType = "currency";
                                }
                                // Number detection
                                else if (headerText.includes("jam") ||
                                         headerText.includes("prod") ||
                                         headerText.includes("acv") ||
                                         headerText.includes("ff") ||
                                         headerText.includes("fpi") ||
                                         headerText.includes("type") ||
                                         /^[0-9.,]+$/.test(cellText)) {
                                    dataType = "number";
                                }
                                // Date detection
                                else if (headerText.includes("tanggal") ||
                                         headerText.includes("date") ||
                                         /^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}$/.test(cellText) ||
                                         /^\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}$/.test(cellText)) {
                                    dataType = "date";
                                }
                                
                                // Apply data type attribute
                                cell.setAttribute("data-type", dataType);
                                
                                // Format currency values
                                if (dataType === "currency" && cellText.includes("Rp.")) {
                                    // Ensure proper spacing for currency
                                    cell.style.textAlign = "right";
                                    cell.style.fontFamily = "Courier New, monospace";
                                    cell.style.fontWeight = "600";
                                    cell.style.color = "#2e7d32";
                                }
                                
                                // Format number values
                                if (dataType === "number" && /^[0-9.,]+$/.test(cellText)) {
                                    cell.style.textAlign = "right";
                                    cell.style.fontFamily = "Courier New, monospace";
                                    cell.style.fontWeight = "500";
                                }
                                
                                // Format date values
                                if (dataType === "date") {
                                    cell.style.textAlign = "center";
                                    cell.style.color = "#666";
                                }
                            });
                        }
                        
                        // Auto-adjust column widths
                        const allCells = table.querySelectorAll("td, th");
                        const columnWidths = {};
                        
                        allCells.forEach(function(cell, index) {
                            const columnIndex = index % headerCells.length;
                            const cellWidth = cell.textContent.length;
                            
                            if (!columnWidths[columnIndex] || cellWidth > columnWidths[columnIndex]) {
                                columnWidths[columnIndex] = cellWidth;
                            }
                        });
                        
                        // Apply minimum and maximum widths
                        Object.keys(columnWidths).forEach(function(columnIndex) {
                            const width = Math.max(80, Math.min(200, columnWidths[columnIndex] * 8));
                            const cells = table.querySelectorAll(`td:nth-child(${parseInt(columnIndex) + 1}), th:nth-child(${parseInt(columnIndex) + 1})`);
                            cells.forEach(function(cell) {
                                cell.style.minWidth = width + "px";
                                cell.style.maxWidth = width + "px";
                            });
                        });
                    });
                    
                    // Add compact mode for tables with many columns
                    const tablesWithManyColumns = document.querySelectorAll(".excel-preview table");
                    tablesWithManyColumns.forEach(function(table) {
                        const columnCount = table.querySelectorAll("th").length;
                        if (columnCount > 10) {
                            table.closest(".excel-preview").classList.add("compact");
                        }
                    });
                });
                </script>
            </body>
            </html>';
            
            return response($fullHtml, 200, [
                'Content-Type' => 'text/html',
                'Cache-Control' => 'public, max-age=3600'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error previewing Excel: ' . $e->getMessage(), [
                'attachment_id' => $attachmentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Try to get attachment info for error display
            $attachmentName = 'Unknown File';
            try {
                $attachment = PoAttachment::find($attachmentId);
                if ($attachment) {
                    $attachmentName = $attachment->original_name;
                }
            } catch (\Exception $attachmentError) {
                \Log::error('Error getting attachment info: ' . $attachmentError->getMessage());
            }
            
            // Return a user-friendly error page
            $errorHtml = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Excel Preview Error</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fa; }
                    .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; margin: 20px auto; max-width: 600px; }
                    .error h3 { margin-top: 0; color: #721c24; }
                    .error p { margin-bottom: 15px; line-height: 1.5; }
                    .btn { display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; font-weight: 500; }
                    .btn:hover { background-color: #0056b3; text-decoration: none; color: white; }
                    .icon { font-size: 48px; margin-bottom: 20px; }
                    .error-details { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; font-family: monospace; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class="error">
                    <div class="icon">⚠️</div>
                    <h3>Excel Preview Error</h3>
                    <p><strong>File:</strong> ' . $attachmentName . '</p>
                    <p><strong>Error:</strong> ' . $e->getMessage() . '</p>
                    <p>This file cannot be previewed due to a technical error. This might be because:</p>
                    <ul>
                        <li>The file format is not supported</li>
                        <li>The file is corrupted</li>
                        <li>The file is too large to process</li>
                        <li>There is a temporary server issue</li>
                    </ul>
                    <div class="error-details">
                        <strong>Technical Details:</strong><br>
                        Attachment ID: ' . $attachmentId . '<br>
                        Error: ' . $e->getMessage() . '
                    </div>
                    <a href="javascript:history.back()" class="btn">← Go Back</a>
                    <a href="' . route('procurement.po.view-attachment', $attachmentId) . '" class="btn" style="margin-left: 10px;">Download File</a>
                </div>
            </body>
            </html>';
            
            return response($errorHtml, 200, [
                'Content-Type' => 'text/html'
            ]);
        }
    }
}
