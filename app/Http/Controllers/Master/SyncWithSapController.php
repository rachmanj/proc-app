<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\PoTemp;
use App\Models\PrTemp;
use App\Models\SyncLog;
use App\Services\SapSyncService;
use Illuminate\Http\Request;

class SyncWithSapController extends Controller
{
    public function __construct(
        private SapSyncService $sapSyncService
    ) {}

    public function index()
    {
        $lastPrSync = SyncLog::where('data_type', 'PR')
            ->orderBy('created_at', 'desc')
            ->first();

        $lastPoSync = SyncLog::where('data_type', 'PO')
            ->orderBy('created_at', 'desc')
            ->first();

        $recentSyncLogs = SyncLog::with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('master.sync-with-sap.index', compact('lastPrSync', 'lastPoSync', 'recentSyncLogs'));
    }

    public function syncPr(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $result = $this->sapSyncService->syncPr($startDate, $endDate, auth()->id());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'sync_log' => $result['sync_log'],
            ]);
        }

        $status = str_contains($result['message'], 'No data found') ? 200 : 500;

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'sync_log' => $result['sync_log'],
        ], $status);
    }

    public function syncPo(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $result = $this->sapSyncService->syncPo($startDate, $endDate, auth()->id());

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'sync_log' => $result['sync_log'],
            ]);
        }

        $status = str_contains($result['message'], 'No data found') ? 200 : 500;

        return response()->json([
            'success' => false,
            'message' => $result['message'],
            'sync_log' => $result['sync_log'],
        ], $status);
    }

    public function truncatePrTemp()
    {
        try {
            PrTemp::truncate();

            return response()->json([
                'success' => true,
                'message' => 'PR temporary table cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing PR temporary table: '.$e->getMessage(),
            ], 500);
        }
    }

    public function truncatePoTemp()
    {
        try {
            PoTemp::truncate();

            return response()->json([
                'success' => true,
                'message' => 'PO temporary table cleared successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing PO temporary table: '.$e->getMessage(),
            ], 500);
        }
    }
}
