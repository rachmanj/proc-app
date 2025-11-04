<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderApproval;
use App\Models\Supplier;
use App\Models\ItemPrice;
use App\Models\Approver;
use App\Models\ApprovalLevel;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isApprover = $user->approvers()->exists();
        
        return view('dashboard.index', compact('isApprover'));
    }

    public function metrics()
    {
        $metrics = Cache::remember('dashboard.metrics', 300, function () {
            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();
            $startOfYear = $now->copy()->startOfYear();

            $prStats = PurchaseRequest::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN pr_status = "OPEN" THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN pr_status = "progress" THEN 1 ELSE 0 END) as progress,
                SUM(CASE WHEN pr_status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN pr_status = "CLOSED" THEN 1 ELSE 0 END) as closed
            ')->first();

            $poStats = PurchaseOrder::selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "draft" THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN status = "submitted" THEN 1 ELSE 0 END) as submitted,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status = "revision" THEN 1 ELSE 0 END) as revision
            ')->first();

            $pendingApprovals = PurchaseOrderApproval::where('status', 'pending')->count();

            // Calculate PO value from purchase_order_details (sum of item_amount)
            // Include all POs (not just approved) for monthly/yearly values
            $monthlyPoValue = DB::table('purchase_orders as po')
                ->leftJoin('purchase_order_details as pod', 'po.id', '=', 'pod.purchase_order_id')
                ->where('po.create_date', '>=', $startOfMonth)
                ->sum('pod.item_amount') ?? 0;

            $yearlyPoValue = DB::table('purchase_orders as po')
                ->leftJoin('purchase_order_details as pod', 'po.id', '=', 'pod.purchase_order_id')
                ->where('po.create_date', '>=', $startOfYear)
                ->sum('pod.item_amount') ?? 0;

            $activeSuppliers = Supplier::whereHas('purchaseOrders')->count();
            $itemsInConsignment = ItemPrice::count();

            $avgApprovalTime = $this->calculateAverageApprovalTime();

            return [
                'pr' => [
                    'total' => (int) $prStats->total,
                    'open' => (int) $prStats->open,
                    'progress' => (int) $prStats->progress,
                    'approved' => (int) $prStats->approved,
                    'closed' => (int) $prStats->closed,
                ],
                'po' => [
                    'total' => (int) $poStats->total,
                    'draft' => (int) $poStats->draft,
                    'submitted' => (int) $poStats->submitted,
                    'approved' => (int) $poStats->approved,
                    'rejected' => (int) $poStats->rejected,
                    'revision' => (int) $poStats->revision,
                ],
                'pending_approvals' => $pendingApprovals,
                'average_approval_time' => round($avgApprovalTime, 2),
                'po_value' => [
                    'monthly' => (float) $monthlyPoValue,
                    'yearly' => (float) $yearlyPoValue,
                ],
                'active_suppliers' => $activeSuppliers,
                'items_in_consignment' => $itemsInConsignment,
            ];
        });

        return response()->json($metrics);
    }

    public function prStatusChart()
    {
        $data = Cache::remember('dashboard.charts.pr-status', 300, function () {
            $stats = PurchaseRequest::selectRaw('
                pr_status,
                COUNT(*) as count
            ')
            ->groupBy('pr_status')
            ->get();

            $labels = [];
            $values = [];
            $colors = [
                'OPEN' => '#007bff',
                'progress' => '#ffc107',
                'approved' => '#28a745',
                'CLOSED' => '#6c757d',
            ];

            foreach ($stats as $stat) {
                $labels[] = ucfirst(strtolower($stat->pr_status));
                $values[] = (int) $stat->count;
            }

            return [
                'labels' => $labels,
                'values' => $values,
                'colors' => array_values($colors),
            ];
        });

        return response()->json($data);
    }

    public function poTrendChart()
    {
        $data = Cache::remember('dashboard.charts.po-trend', 300, function () {
            $days = 30;
            $startDate = Carbon::now()->subDays($days);

            $created = PurchaseOrder::selectRaw('
                DATE(create_date) as date,
                COUNT(*) as count
            ')
            ->where('create_date', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

            $approved = PurchaseOrder::selectRaw('
                DATE(updated_at) as date,
                COUNT(*) as count
            ')
            ->where('status', 'approved')
            ->where('updated_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

            $labels = [];
            $createdData = [];
            $approvedData = [];

            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $labels[] = Carbon::parse($date)->format('M d');
                $createdData[] = isset($created[$date]) ? (int) $created[$date] : 0;
                $approvedData[] = isset($approved[$date]) ? (int) $approved[$date] : 0;
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Created',
                        'data' => $createdData,
                        'backgroundColor' => '#007bff',
                        'borderColor' => '#007bff',
                    ],
                    [
                        'label' => 'Approved',
                        'data' => $approvedData,
                        'backgroundColor' => '#28a745',
                        'borderColor' => '#28a745',
                    ],
                ],
            ];
        });

        return response()->json($data);
    }

    public function approvalTimeChart()
    {
        $data = Cache::remember('dashboard.charts.approval-time', 300, function () {
            $avgByLevel = PurchaseOrderApproval::selectRaw('
                approval_levels.name as level_name,
                AVG(TIMESTAMPDIFF(HOUR, purchase_order_approvals.created_at, purchase_order_approvals.approved_at)) as avg_hours
            ')
            ->join('approval_levels', 'purchase_order_approvals.approval_level_id', '=', 'approval_levels.id')
            ->where('purchase_order_approvals.status', 'approved')
            ->whereNotNull('purchase_order_approvals.approved_at')
            ->groupBy('approval_levels.id', 'approval_levels.name')
            ->orderBy('approval_levels.level')
            ->get();

            $labels = [];
            $values = [];

            foreach ($avgByLevel as $level) {
                $labels[] = $level->level_name;
                $values[] = round((float) $level->avg_hours, 2);
            }

            return [
                'labels' => $labels,
                'values' => $values,
            ];
        });

        return response()->json($data);
    }

    public function topSuppliersChart()
    {
        $data = Cache::remember('dashboard.charts.top-suppliers', 300, function () {
            // Calculate PO value from purchase_order_details (sum of item_amount)
            // Include all POs (not just approved) to show top suppliers
            $topSuppliers = DB::table('purchase_orders as po')
                ->join('suppliers as s', 'po.supplier_id', '=', 's.id')
                ->leftJoin('purchase_order_details as pod', 'po.id', '=', 'pod.purchase_order_id')
                ->select(
                    's.name',
                    DB::raw('COALESCE(SUM(pod.item_amount), 0) as total_value')
                )
                ->groupBy('s.id', 's.name')
                ->orderBy('total_value', 'desc')
                ->limit(20)
                ->get();

            $labels = [];
            $values = [];

            foreach ($topSuppliers as $supplier) {
                if ((float) $supplier->total_value > 0) {
                    $labels[] = $supplier->name;
                    $values[] = (float) $supplier->total_value;
                }
            }

            return [
                'labels' => $labels,
                'values' => $values,
            ];
        });

        return response()->json($data);
    }

    public function departmentPrChart()
    {
        $data = Cache::remember('dashboard.charts.department-pr', 300, function () {
            $stats = PurchaseRequest::selectRaw('
                dept_name,
                pr_status,
                COUNT(*) as count
            ')
            ->whereNotNull('dept_name')
            ->groupBy('dept_name', 'pr_status')
            ->get();

            $departments = $stats->pluck('dept_name')->unique()->values();
            $statuses = ['OPEN', 'progress', 'approved', 'CLOSED'];

            $datasets = [];
            $colors = [
                'OPEN' => '#007bff',
                'progress' => '#ffc107',
                'approved' => '#28a745',
                'CLOSED' => '#6c757d',
            ];

            foreach ($statuses as $status) {
                $data = [];
                foreach ($departments as $dept) {
                    $count = $stats->where('dept_name', $dept)
                        ->where('pr_status', $status)
                        ->first();
                    $data[] = $count ? (int) $count->count : 0;
                }
                $datasets[] = [
                    'label' => ucfirst(strtolower($status)),
                    'data' => $data,
                    'backgroundColor' => $colors[$status],
                ];
            }

            return [
                'labels' => $departments->toArray(),
                'datasets' => $datasets,
            ];
        });

        return response()->json($data);
    }

    public function activity()
    {
        $user = auth()->user();
        $isApprover = $user->approvers()->exists();

        $recentPRs = PurchaseRequest::with('details')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($pr) {
                return [
                    'id' => $pr->id,
                    'pr_no' => $pr->pr_no ?? $pr->pr_draft_no,
                    'status' => $pr->pr_status,
                    'requestor' => $pr->requestor,
                    'department' => $pr->dept_name,
                    'created_at' => $pr->created_at->format('Y-m-d H:i'),
                    'url' => route('procurement.pr.show', $pr),
                ];
            });

        $recentPOs = PurchaseOrder::with(['supplier', 'purchaseOrderDetails'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($po) {
                // Calculate PO value from purchase_order_details (sum of item_amount)
                $totalValue = $po->purchaseOrderDetails->sum('item_amount') ?? 0;
                return [
                    'id' => $po->id,
                    'doc_num' => $po->doc_num,
                    'status' => $po->status,
                    'supplier' => $po->supplier->name ?? 'N/A',
                    'total_value' => number_format($totalValue, 0, ',', '.'),
                    'created_at' => $po->created_at->format('Y-m-d H:i'),
                    'url' => route('procurement.po.show', $po),
                ];
            });

        $recentApprovals = PurchaseOrderApproval::with(['purchaseOrder.supplier', 'approval_level'])
            ->where('status', 'approved')
            ->orderBy('approved_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($approval) {
                return [
                    'id' => $approval->id,
                    'po_id' => $approval->purchase_order_id,
                    'po_number' => $approval->purchaseOrder->doc_num ?? 'N/A',
                    'level' => $approval->approval_level->name ?? 'N/A',
                    'approved_at' => $approval->approved_at ? Carbon::parse($approval->approved_at)->format('Y-m-d H:i') : 'N/A',
                    'url' => route('procurement.po.show', $approval->purchaseOrder),
                ];
            });

        $pendingApprovalsForUser = [];
        if ($isApprover) {
            $userApprovalLevelIds = $user->approvers()->pluck('approval_level_id');
            $pendingApprovalsForUser = PurchaseOrderApproval::with(['purchaseOrder.supplier', 'approval_level'])
                ->where('status', 'pending')
                ->whereIn('approval_level_id', $userApprovalLevelIds)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($approval) {
                    return [
                        'id' => $approval->id,
                        'po_id' => $approval->purchase_order_id,
                        'po_number' => $approval->purchaseOrder->doc_num ?? 'N/A',
                        'supplier' => $approval->purchaseOrder->supplier->name ?? 'N/A',
                        'level' => $approval->approval_level->name ?? 'N/A',
                        'created_at' => $approval->created_at->format('Y-m-d H:i'),
                        'url' => route('approvals.po.show', $approval->purchaseOrder),
                    ];
                });
        }

        return response()->json([
            'recent_prs' => $recentPRs,
            'recent_pos' => $recentPOs,
            'recent_approvals' => $recentApprovals,
            'pending_approvals' => $pendingApprovalsForUser,
        ]);
    }

    private function calculateAverageApprovalTime()
    {
        $avgTime = PurchaseOrderApproval::selectRaw('
            AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours
        ')
        ->where('status', 'approved')
        ->whereNotNull('approved_at')
        ->value('avg_hours');

        return $avgTime ? (float) $avgTime : 0;
    }
}
