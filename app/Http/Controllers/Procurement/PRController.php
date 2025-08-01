<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Session;
use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use App\Models\PrAttachment;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Alert;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;

class PRController extends Controller
{
    public function index()
    {
        $page = request()->query('page', 'dashboard');

        $views = [
            'dashboard' => 'procurement.pr.dashboard',
            'search' => 'procurement.pr.search',
            'create' => 'procurement.pr.create',
            'list' => 'procurement.pr.list',
        ];

        if ($page == 'search') {
            return $this->searchPage();
        }

        if ($page == 'dashboard') {
            $dashboardData = $this->getDashboardData();
            return view($views[$page], $dashboardData);
        }

        if ($page == 'list') {
            $purchaseRequests = PurchaseRequest::orderBy('created_at', 'desc')->get();
            return view($views[$page], compact('purchaseRequests'));
        }

        return view($views[$page]);
    }

    public function getDashboardData()
    {
        // Get distinct project codes ordered
        $projectCodes = PurchaseRequest::distinct()
            ->orderBy('project_code')
            ->pluck('project_code')
            ->toArray();

        $prCountsByProject = PurchaseRequest::select('project_code', DB::raw('count(*) as total'))
            ->groupBy('project_code')
            ->get()
            ->pluck('total', 'project_code')
            ->toArray();

        $openPrCountsByProject = PurchaseRequest::where('pr_status', 'OPEN')
            ->select('project_code', DB::raw('count(*) as total'))
            ->groupBy('project_code')
            ->get()
            ->pluck('total', 'project_code')
            ->toArray();

        $totalPRs = array_sum($prCountsByProject);
        $totalOpenPRs = array_sum($openPrCountsByProject);

        return compact(
            'projectCodes',
            'prCountsByProject',
            'openPrCountsByProject',
            'totalPRs',
            'totalOpenPRs'
        );
    }

    public function searchPage()
    {
        // Get unique values for dropdowns and order them
        $priorities = PurchaseRequest::distinct()->orderBy('priority')->pluck('priority');
        $statuses = PurchaseRequest::distinct()->orderBy('pr_status')->pluck('pr_status');
        $types = PurchaseRequest::distinct()->orderBy('pr_type')->pluck('pr_type');
        $projectCodes = PurchaseRequest::distinct()->orderBy('project_code')->pluck('project_code');
        $units = PurchaseRequest::distinct()->orderBy('for_unit')->pluck('for_unit');

        // Get stored search parameters
        $searchParams = Session::get('pr_search', []);

        return view('procurement.pr.search', compact(
            'priorities',
            'statuses',
            'types',
            'projectCodes',
            'units',
            'searchParams'
        ));
    }

    public function search(Request $request)
    {
        // Store search parameters in session
        Session::put('pr_search', [
            'pr_no' => $request->pr_no,
            'pr_draft_no' => $request->pr_draft_no,
            'pr_rev_no' => $request->pr_rev_no,
            'priority' => $request->priority,
            'pr_status' => $request->pr_status,
            'pr_type' => $request->pr_type,
            'project_code' => $request->project_code,
            'for_unit' => $request->for_unit,
        ]);

        $query = PurchaseRequest::query();

        // Apply filters
        if ($request->pr_no) {
            $query->where('pr_no', 'like', '%' . $request->pr_no . '%');
        }
        if ($request->pr_draft_no) {
            $query->where('pr_draft_no', 'like', '%' . $request->pr_draft_no . '%');
        }
        if ($request->pr_rev_no) {
            $query->where('pr_rev_no', 'like', '%' . $request->pr_rev_no . '%');
        }
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }
        if ($request->pr_status) {
            $query->where('pr_status', $request->pr_status);
        }
        if ($request->pr_type) {
            $query->where('pr_type', $request->pr_type);
        }
        if ($request->project_code) {
            $query->where('project_code', $request->project_code);
        }
        if ($request->for_unit) {
            $query->where('for_unit', $request->for_unit);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('day', function ($model) {
                return $model->day_difference;
            })
            ->addColumn('action', function ($model) {
                return view('procurement.pr.action', compact('model'))->render();
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        // Load the purchase request details relationship
        $purchaseRequest->load('details');

        return view('procurement.pr.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        // Load the purchase request details and attachments relationships
        $purchaseRequest->load(['details', 'attachments']);

        return view('procurement.pr.edit', compact('purchaseRequest'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $validated = $request->validate([
            'pr_no' => 'required|string|max:30',
            'pr_draft_no' => 'nullable|string|max:30',
            'pr_rev_no' => 'nullable|string|max:30',
            'priority' => 'required|string|in:NORMAL,URGENT',
            'pr_status' => 'required|string|in:OPEN,CLOSED,progress,approved',
            'pr_type' => 'nullable|string|max:50',
            'project_code' => 'nullable|string|max:30',
            'for_unit' => 'nullable|string|max:50',
            'remarks' => 'nullable|string'
        ]);

        try {
            $purchaseRequest->update($validated);

            return redirect()->route('procurement.pr.show', $purchaseRequest)
                ->with('success', 'Purchase Request updated successfully');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error updating Purchase Request: ' . $e->getMessage());
        }
    }

    public function clearSearch()
    {
        Session::forget('pr_search');
        return response()->json(['success' => true]);
    }

    public function getAttachments(PurchaseRequest $purchaseRequest)
    {
        try {
            $attachments = $purchaseRequest->attachments()
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($attachment) {
                    return [
                        'id' => $attachment->id,
                        'original_name' => $attachment->original_name,
                        'file_path' => $attachment->file_path,
                        'file_type' => $attachment->file_type,
                        'file_size' => $attachment->file_size,
                        'keterangan' => $attachment->keterangan,
                        'created_at' => $attachment->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $attachment->updated_at->format('Y-m-d H:i:s')
                    ];
                });

            return response()->json([
                'success' => true,
                'attachments' => $attachments
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting attachments: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error getting attachments: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadAttachments(Request $request, PurchaseRequest $purchaseRequest)
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
                    $path = $file->store('pr_attachments', 'public');
                    
                    // Create attachment record
                    $attachment = PrAttachment::create([
                        'original_name' => $file->getClientOriginalName(),
                        'file_path' => $path,
                        'file_type' => $file->getClientMimeType(),
                        'file_size' => $file->getSize(),
                        'keterangan' => $descriptions[$index] ?? null,
                        'pr_no' => $purchaseRequest->pr_no
                    ]);
                    
                    // Attach to purchase request via pivot table
                    $purchaseRequest->attachments()->attach($attachment->id);
                    
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

    public function updateAttachment(Request $request, PrAttachment $attachment)
    {
        try {
            $request->validate([
                'keterangan' => 'nullable|string|max:500',
                'file' => 'nullable|file|max:10240' // Max 10MB
            ]);

            if ($request->has('keterangan')) {
                $attachment->keterangan = $request->input('keterangan');
            }

            if ($request->hasFile('file')) {
                if (Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }

                $file = $request->file('file');
                $path = $file->store('pr-attachments', 'public');
                
                $attachment->original_name = $file->getClientOriginalName();
                $attachment->file_path = $path;
                $attachment->file_type = $file->getClientMimeType();
                $attachment->file_size = $file->getSize();
            }

            $attachment->save();

            return response()->json([
                'success' => true,
                'message' => 'Attachment updated successfully',
                'attachment' => $attachment
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating attachment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update attachment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function detachFile(PrAttachment $attachment)
    {
        try {
            // Get the purchase request that owns this attachment
            $purchaseRequest = $attachment->purchaseRequests()->first();
            
            if (!$purchaseRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attachment not found in any purchase request'
                ], 404);
            }

            // Detach the attachment from the purchase request
            $purchaseRequest->attachments()->detach($attachment->id);

            // Delete the file from storage
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            // Delete the attachment record
            $attachment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Attachment deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting attachment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting attachment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function viewAttachment(PrAttachment $attachment)
    {
        try {
            // Check if file exists in storage
            if (!Storage::disk('public')->exists($attachment->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
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
            \Log::error('Error viewing attachment: ' . $e->getMessage(), [
                'attachment_id' => $attachment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error viewing attachment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function previewExcel(PrAttachment $attachment)
    {
        try {
            // Check if file exists in storage
            if (!Storage::disk('public')->exists($attachment->file_path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
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
                'attachment_id' => $attachment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a simple HTML error page
            $errorHtml = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Excel Preview Error</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; }
                    .error h3 { margin-top: 0; }
                    .error p { margin-bottom: 10px; }
                    .btn { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px; }
                </style>
            </head>
            <body>
                <div class="error">
                    <h3>Excel Preview Error</h3>
                    <p><strong>File:</strong> ' . $attachment->original_name . '</p>
                    <p><strong>Error:</strong> ' . $e->getMessage() . '</p>
                    <p>This file cannot be previewed. Please download it to view the contents.</p>
                    <a href="' . route('procurement.pr.view-attachment', $attachment->id) . '" class="btn">Download File</a>
                </div>
            </body>
            </html>';
            
            return response($errorHtml, 200, [
                'Content-Type' => 'text/html'
            ]);
        }
    }

    public function data()
    {
        return DataTables::of(PurchaseRequest::query())
            ->addIndexColumn()
            ->addColumn('day', function ($model) {
                return $model->day_difference;
            })
            ->addColumn('action', function ($model) {
                return view('procurement.pr.action', compact('model'))->render();
            })
            ->rawColumns(['action', 'pr_status'])
            ->make(true);
    }
}
