<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Imports\PRTempImport;
use App\Models\PrTemp;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class DailyPRController extends Controller
{
    public function index()
    {
        $page = request()->query('page', 'dashboard');

        $views = [
            'dashboard' => 'master.dailypr.dashboard',
            'search' => 'master.dailypr.search',
            'create' => 'master.dailypr.create',
            'list' => 'master.dailypr.list',
        ];

        return view($views[$page]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            // Clear existing temporary data
            PrTemp::truncate();

            // Import new data
            $import = new PRTempImport;
            Excel::import($import, $request->file('file'));

            $rowCount = PrTemp::count(); // Get count of imported rows

            return response()->json([
                'success' => true,
                'message' => 'Data imported successfully',
                'rowCount' => $rowCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function data()
    {
        $query = PrTemp::select([
            'pr_no',
            'pr_date',
            'project_code',
            'dept_name',
            'item_name',
            'Quantity',
            'uom',
            'pr_status'
        ]);

        return DataTables::of($query)->toJson();
    }
}
