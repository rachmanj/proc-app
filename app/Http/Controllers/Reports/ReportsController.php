<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderApproval;
use App\Models\ApprovalLevel;
use App\Models\Department;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // PR Reports
    public function prStatus(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $query = PurchaseRequest::whereBetween('generated_date', [$dateFrom, $dateTo]);

        $statusCounts = Cache::remember("pr_status_report_{$dateFrom}_{$dateTo}", 300, function () use ($query) {
            return $query->select('pr_status', DB::raw('count(*) as count'))
                ->groupBy('pr_status')
                ->pluck('count', 'pr_status')
                ->toArray();
        });

        $totalPrs = array_sum($statusCounts);

        return view('reports.pr.status', compact('statusCounts', 'totalPrs', 'dateFrom', 'dateTo'));
    }

    public function prAging(Request $request)
    {
        $query = PurchaseRequest::whereIn('pr_status', ['OPEN', 'progress'])
            ->selectRaw('
                CASE 
                    WHEN DATEDIFF(NOW(), generated_date) <= 7 THEN "0-7 days"
                    WHEN DATEDIFF(NOW(), generated_date) <= 14 THEN "8-14 days"
                    WHEN DATEDIFF(NOW(), generated_date) <= 30 THEN "15-30 days"
                    WHEN DATEDIFF(NOW(), generated_date) <= 60 THEN "31-60 days"
                    ELSE "60+ days"
                END as age_range,
                COUNT(*) as count
            ')
            ->groupBy('age_range')
            ->orderByRaw('
                CASE age_range
                    WHEN "0-7 days" THEN 1
                    WHEN "8-14 days" THEN 2
                    WHEN "15-30 days" THEN 3
                    WHEN "31-60 days" THEN 4
                    ELSE 5
                END
            ');

        $agingData = Cache::remember('pr_aging_report', 300, function () use ($query) {
            return $query->get();
        });

        $overduePrs = PurchaseRequest::whereIn('pr_status', ['OPEN', 'progress'])
            ->whereRaw('DATEDIFF(NOW(), generated_date) > 30')
            ->count();

        return view('reports.pr.aging', compact('agingData', 'overduePrs'));
    }

    public function prByDepartment(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $departmentData = Cache::remember("pr_department_report_{$dateFrom}_{$dateTo}", 300, function () use ($dateFrom, $dateTo) {
            return PurchaseRequest::whereBetween('generated_date', [$dateFrom, $dateTo])
                ->select('dept_name', 'pr_status', DB::raw('count(*) as count'))
                ->groupBy('dept_name', 'pr_status')
                ->orderBy('dept_name')
                ->get()
                ->groupBy('dept_name');
        });

        return view('reports.pr.by-department', compact('departmentData', 'dateFrom', 'dateTo'));
    }

    public function prByProject(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $projectData = Cache::remember("pr_project_report_{$dateFrom}_{$dateTo}", 300, function () use ($dateFrom, $dateTo) {
            return PurchaseRequest::whereBetween('generated_date', [$dateFrom, $dateTo])
                ->select('project_code', DB::raw('count(*) as count'))
                ->groupBy('project_code')
                ->orderBy('count', 'desc')
                ->take(20)
                ->get();
        });

        return view('reports.pr.by-project', compact('projectData', 'dateFrom', 'dateTo'));
    }

    public function prApprovalTime(Request $request)
    {
        // This would need approval data for PRs - placeholder for now
        return view('reports.pr.approval-time');
    }

    // PO Reports
    public function poStatus(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $statusCounts = Cache::remember("po_status_report_{$dateFrom}_{$dateTo}", 300, function () use ($dateFrom, $dateTo) {
            return PurchaseOrder::whereBetween('doc_date', [$dateFrom, $dateTo])
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
        });

        $totalPos = array_sum($statusCounts);

        return view('reports.po.status', compact('statusCounts', 'totalPos', 'dateFrom', 'dateTo'));
    }

    public function poValue(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $groupBy = $request->get('group_by', 'month'); // month, week, day

        $valueData = Cache::remember("po_value_report_{$dateFrom}_{$dateTo}_{$groupBy}", 300, function () use ($dateFrom, $dateTo, $groupBy) {
            $format = match($groupBy) {
                'day' => '%Y-%m-%d',
                'week' => '%Y-%u',
                default => '%Y-%m'
            };

            // Calculate PO value from purchase_order_details (sum of item_amount) using join
            return DB::table('purchase_orders as po')
                ->leftJoin('purchase_order_details as pod', 'po.id', '=', 'pod.purchase_order_id')
                ->whereBetween('po.doc_date', [$dateFrom, $dateTo])
                ->select(
                    DB::raw("DATE_FORMAT(po.doc_date, '{$format}') as period"),
                    DB::raw('COUNT(DISTINCT po.id) as count'),
                    DB::raw('COALESCE(SUM(pod.item_amount), 0) as total_value')
                )
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->map(function ($item) {
                    return (object)[
                        'period' => $item->period,
                        'total_value' => (float) $item->total_value,
                        'count' => (int) $item->count
                    ];
                });
        });

        return view('reports.po.value', compact('valueData', 'dateFrom', 'dateTo', 'groupBy'));
    }

    public function poBySupplier(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $supplierData = Cache::remember("po_supplier_report_{$dateFrom}_{$dateTo}", 300, function () use ($dateFrom, $dateTo) {
            // Calculate PO value from purchase_order_details (sum of item_amount) using join
            return DB::table('purchase_orders as po')
                ->join('suppliers as s', 'po.supplier_id', '=', 's.id')
                ->leftJoin('purchase_order_details as pod', 'po.id', '=', 'pod.purchase_order_id')
                ->whereBetween('po.doc_date', [$dateFrom, $dateTo])
                ->select(
                    'po.supplier_id',
                    's.name as supplier_name',
                    DB::raw('COUNT(DISTINCT po.id) as count'),
                    DB::raw('COALESCE(SUM(pod.item_amount), 0) as total_value')
                )
                ->groupBy('po.supplier_id', 's.name')
                ->orderBy('total_value', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($item) {
                    return [
                        'supplier_name' => $item->supplier_name ?? 'Unknown',
                        'total_value' => (float) $item->total_value,
                        'count' => (int) $item->count
                    ];
                })
                ->values();
        });

        return view('reports.po.by-supplier', compact('supplierData', 'dateFrom', 'dateTo'));
    }

    public function poDeliveryStatus(Request $request)
    {
        // Placeholder - would need delivery tracking data
        return view('reports.po.delivery-status');
    }

    // Approval Reports
    public function approvalTimeAnalysis(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $timeData = Cache::remember("approval_time_report_{$dateFrom}_{$dateTo}", 300, function () use ($dateFrom, $dateTo) {
            return PurchaseOrderApproval::with('approval_level')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'approved')
                ->whereNotNull('approved_at')
                ->selectRaw('
                    approval_level_id,
                    AVG(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as avg_hours,
                    MIN(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as min_hours,
                    MAX(TIMESTAMPDIFF(HOUR, created_at, approved_at)) as max_hours,
                    COUNT(*) as count
                ')
                ->groupBy('approval_level_id')
                ->get()
                ->map(function ($item) {
                    return [
                        'level_name' => $item->approval_level->name ?? 'Unknown',
                        'avg_hours' => round($item->avg_hours, 2),
                        'min_hours' => $item->min_hours,
                        'max_hours' => $item->max_hours,
                        'count' => $item->count
                    ];
                });
        });

        return view('reports.approval.time-analysis', compact('timeData', 'dateFrom', 'dateTo'));
    }

    public function approvalByApprover(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $approverData = Cache::remember("approval_by_approver_{$dateFrom}_{$dateTo}", 300, function () use ($dateFrom, $dateTo) {
            return PurchaseOrderApproval::with('approver.user')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('
                    approver_id,
                    status,
                    COUNT(*) as count,
                    AVG(CASE WHEN status = "approved" AND approved_at IS NOT NULL 
                        THEN TIMESTAMPDIFF(HOUR, created_at, approved_at) 
                        ELSE NULL END) as avg_hours
                ')
                ->groupBy('approver_id', 'status')
                ->get()
                ->groupBy('approver_id')
                ->map(function ($group) {
                    $firstItem = $group->first();
                    return [
                        'approver_name' => $firstItem->approver->user->name ?? 'Unknown',
                        'approved_count' => $group->where('status', 'approved')->sum('count'),
                        'rejected_count' => $group->where('status', 'rejected')->sum('count'),
                        'pending_count' => $group->where('status', 'pending')->sum('count'),
                        'avg_hours' => round($group->where('status', 'approved')->avg('avg_hours') ?? 0, 2)
                    ];
                })
                ->take(20);
        });

        return view('reports.approval.by-approver', compact('approverData', 'dateFrom', 'dateTo'));
    }

    public function approvalBottleneck(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $bottleneckData = Cache::remember("approval_bottleneck_{$dateFrom}_{$dateTo}", 300, function () use ($dateFrom, $dateTo) {
            return PurchaseOrderApproval::with('approval_level')
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'pending')
                ->selectRaw('
                    approval_level_id,
                    COUNT(*) as pending_count,
                    AVG(TIMESTAMPDIFF(HOUR, created_at, NOW())) as avg_waiting_hours
                ')
                ->groupBy('approval_level_id')
                ->orderBy('pending_count', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'level_name' => $item->approval_level->name ?? 'Unknown',
                        'pending_count' => $item->pending_count,
                        'avg_waiting_hours' => round($item->avg_waiting_hours, 2)
                    ];
                });
        });

        return view('reports.approval.bottleneck', compact('bottleneckData', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request, $type)
    {
        // Export functionality - would use Laravel Excel package
        // Placeholder for now
        return response()->json(['message' => 'Export functionality coming soon']);
    }
}
