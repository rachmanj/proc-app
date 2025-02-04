<?php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\PurchaseRequest;
use Illuminate\Support\Facades\Session;

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

    public function clearSearch()
    {
        Session::forget('pr_search');
        return response()->json(['success' => true]);
    }
}
