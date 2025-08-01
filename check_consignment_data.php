<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PurchaseOrderDetail;
use Illuminate\Support\Facades\DB;

echo "Checking existing CONSIGNMENT data in purchase_order_details...\n\n";

// Find all CONSIGNMENT items
$consignmentItems = PurchaseOrderDetail::where('item_code', 'CONSIGNMENT')->get();

echo "Found " . $consignmentItems->count() . " CONSIGNMENT items\n\n";

foreach ($consignmentItems as $item) {
    echo "ID: {$item->id}\n";
    echo "Purchase Order ID: {$item->purchase_order_id}\n";
    echo "Item Code: {$item->item_code}\n";
    echo "Description: {$item->description}\n";
    echo "Remark1: " . ($item->remark1 ?? 'NULL') . "\n";
    echo "Remark2: " . ($item->remark2 ?? 'NULL') . "\n";
    echo "Remark1 empty?: " . (empty(trim($item->remark1)) ? 'YES' : 'NO') . "\n";
    echo "Remark2 empty?: " . (empty(trim($item->remark2)) ? 'YES' : 'NO') . "\n";
    echo "---\n";
}

// Check what will be displayed with current logic
echo "\nWhat will be displayed with current logic:\n\n";

foreach ($consignmentItems->take(5) as $item) {
    $displayItemCode = !empty(trim($item->remark1)) ? $item->remark1 : $item->item_code;
    $displayDescription = !empty(trim($item->remark2)) ? $item->remark2 : $item->description;
    
    echo "ID {$item->id}:\n";
    echo "  Item Code column will show: '{$displayItemCode}'\n";
    echo "  Description column will show: '{$displayDescription}'\n";
    echo "---\n";
}

echo "\nScript completed!\n"; 