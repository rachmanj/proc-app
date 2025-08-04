<?php

namespace App\Http\Controllers\Consignment;

use App\Http\Controllers\Controller;
use App\Models\ItemPrice;
use App\Models\ItemPriceHistory;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class ConsignmentController extends Controller
{
    public function __construct()
    {
        // Permission is handled in the routes file
    }

    /**
     * Display the consignment dashboard.
     */
    public function index()
    {
        $itemCount = ItemPrice::count();
        $warehouseCount = Warehouse::count();
        $historyCount = ItemPriceHistory::count();
        $recentItems = ItemPrice::with(['supplier', 'uploader'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('consignment.dashboard', compact(
            'itemCount',
            'warehouseCount',
            'historyCount',
            'recentItems'
        ));
    }
}
