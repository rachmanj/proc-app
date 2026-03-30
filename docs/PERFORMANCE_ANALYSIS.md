# Performance Analysis & Optimization Recommendations

**Generated**: 2025-01-27  
**Based on**: Server log analysis and codebase review

## Executive Summary

Analysis of server logs shows frequent API calls every 30 seconds and some requests taking 29 seconds to 1 minute 31 seconds. This document identifies the root causes and provides actionable optimization recommendations.

---

## 1. Critical Issues Identified

### 1.1 Frequent Polling/Server Calls

#### Issue 1: Notification Polling (Every 30 seconds)
**Location**: `resources/views/layout/partials/script.blade.php`

**Problem**:
```javascript
setInterval(function() {
    loadNotifications();
}, 30000); // Polls every 30 seconds on ALL pages
```

**Impact**:
- Every user on every page makes an API call every 30 seconds
- With 10 concurrent users = 20 requests/minute just for notifications
- High server load and database queries

**Current Implementation**:
```php
// app/Http/Controllers/NotificationController.php
public function index()
{
    $user = Auth::user();
    
    $notifications = $user->notifications()
        ->orderBy('created_at', 'desc')
        ->limit(15)
        ->get();

    $unreadCount = $user->unreadNotifications()->count(); // Query runs every time
    
    return response()->json([
        'notifications' => $notifications,
        'unread_count' => $unreadCount,
    ]);
}
```

#### Issue 2: Comment Count Polling (Every 30 seconds)
**Location**: 
- `resources/views/procurement/pr/show.blade.php`
- `resources/views/procurement/po/show.blade.php`

**Problem**:
```javascript
setInterval(loadCommentCounts, 30000); // Polls every 30 seconds on detail pages
```

**Impact**:
- Every user viewing a PR/PO detail page polls comment counts every 30 seconds
- Multiple queries per request (header count + line item counts)

#### Issue 3: Dashboard Activity Endpoint (Not Cached)
**Location**: `app/Http/Controllers/DashboardController.php::activity()`

**Problem**:
- NO CACHING implemented
- Loads recent PRs, POs, approvals, and pending approvals on EVERY dashboard load
- Includes relationship loading which can cause N+1 queries

**Current Code**:
```php
public function activity()
{
    // NOT CACHED - runs expensive queries every time
    $recentPRs = PurchaseRequest::with('details')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    $recentPOs = PurchaseOrder::with(['supplier', 'purchaseOrderDetails'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    $recentApprovals = PurchaseOrderApproval::with(['purchaseOrder.supplier', 'approval_level'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    // ... more queries
}
```

---

### 1.2 Slow Query Issues

#### Issue 4: Missing Database Indexes

**Queries that need indexes**:

1. **Purchase Requests**:
   ```sql
   -- Missing indexes
   CREATE INDEX idx_pr_status_date ON purchase_requests(pr_status, generated_date);
   CREATE INDEX idx_pr_created_at ON purchase_requests(created_at DESC);
   CREATE INDEX idx_pr_dept_status ON purchase_requests(dept_name, pr_status);
   ```

2. **Purchase Orders**:
   ```sql
   -- Missing indexes
   CREATE INDEX idx_po_status_date ON purchase_orders(status, create_date);
   CREATE INDEX idx_po_created_at ON purchase_orders(created_at DESC);
   CREATE INDEX idx_po_supplier_date ON purchase_orders(supplier_id, doc_date);
   ```

3. **Purchase Order Approvals**:
   ```sql
   -- Missing indexes
   CREATE INDEX idx_poa_status_level ON purchase_order_approvals(status, approval_level_id);
   CREATE INDEX idx_poa_created_at ON purchase_order_approvals(created_at DESC);
   CREATE INDEX idx_poa_approved_at ON purchase_order_approvals(approved_at) WHERE approved_at IS NOT NULL;
   ```

4. **Purchase Order Details**:
   ```sql
   -- Missing indexes
   CREATE INDEX idx_pod_po_id ON purchase_order_details(purchase_order_id);
   CREATE INDEX idx_pod_amount ON purchase_order_details(item_amount);
   ```

5. **Notifications**:
   ```sql
   -- Missing indexes
   CREATE INDEX idx_notifications_user_read ON notifications(notifiable_id, read_at) WHERE notifiable_type = 'App\Models\User';
   CREATE INDEX idx_notifications_created_at ON notifications(created_at DESC);
   ```

6. **Comments**:
   ```sql
   -- Missing indexes
   CREATE INDEX idx_comments_commentable ON comments(commentable_type, commentable_id);
   CREATE INDEX idx_comments_line_item ON comments(line_item_id) WHERE line_item_id IS NOT NULL;
   CREATE INDEX idx_comments_parent ON comments(parent_id) WHERE parent_id IS NOT NULL;
   ```

#### Issue 5: Expensive Queries Without Caching

1. **Active Suppliers Query**:
   ```php
   // app/Http/Controllers/DashboardController.php
   $activeSuppliers = Supplier::whereHas('purchaseOrders')->count();
   ```
   - Uses `whereHas()` which can be slow on large datasets
   - Runs on every metrics call (even though cached for 5 minutes)

2. **PO Value Calculations**:
   ```php
   $monthlyPoValue = DB::table('purchase_orders as po')
       ->leftJoin('purchase_order_details as pod', 'po.id', '=', 'pod.purchase_order_id')
       ->where('po.create_date', '>=', $startOfMonth)
       ->sum('pod.item_amount') ?? 0;
   ```
   - Large table scan if indexes missing on `create_date` and `purchase_order_id`

---

## 2. Recommended Optimizations

### 2.1 Immediate Fixes (High Priority)

#### Fix 1: Optimize Notification Polling

**Option A: Increase Poll Interval** (Quick Fix)
- Change from 30 seconds to 2-5 minutes for background polling
- Still responsive but reduces load by 75-87%

**Option B: Smart Polling** (Better Solution)
- Only poll when tab is active
- Use Page Visibility API
- Poll more frequently when user is active, less when inactive

**Option C: Server-Sent Events (SSE) or WebSockets** (Best Solution)
- Push notifications instead of polling
- Real-time updates without constant polling
- Requires more infrastructure

**Implementation (Option B)**:
```javascript
// resources/views/layout/partials/script.blade.php
$(document).ready(function() {
    let pollInterval = 30000; // 30 seconds when active
    let inactiveInterval = 300000; // 5 minutes when inactive
    let notificationTimer;
    
    function loadNotifications() {
        if (document.hidden) return; // Don't poll when tab is hidden
        
        $.ajax({
            url: '{{ route("api.notifications.index") }}',
            method: 'GET',
            success: function(response) {
                updateNotificationUI(response);
            }
        });
    }
    
    // Load immediately
    loadNotifications();
    
    // Adjust interval based on page visibility
    document.addEventListener('visibilitychange', function() {
        clearInterval(notificationTimer);
        const interval = document.hidden ? inactiveInterval : pollInterval;
        notificationTimer = setInterval(loadNotifications, interval);
    });
    
    // Start polling
    notificationTimer = setInterval(loadNotifications, pollInterval);
});
```

**Backend Optimization**:
```php
// app/Http/Controllers/NotificationController.php
public function index()
{
    $user = Auth::user();
    
    // Cache unread count for 30 seconds
    $unreadCount = Cache::remember("user_{$user->id}_unread_notifications", 30, function() use ($user) {
        return $user->unreadNotifications()->count();
    });
    
    $notifications = $user->notifications()
        ->orderBy('created_at', 'desc')
        ->limit(15)
        ->get();
    
    return response()->json([
        'notifications' => $notifications,
        'unread_count' => $unreadCount,
        'cached' => true,
    ]);
}
```

#### Fix 2: Cache Dashboard Activity Endpoint

```php
// app/Http/Controllers/DashboardController.php
public function activity()
{
    $user = auth()->user();
    $isApprover = $user->approvers()->exists();
    
    // Cache for 2 minutes (120 seconds)
    $cacheKey = "dashboard.activity." . ($isApprover ? "approver" : "user");
    
    $data = Cache::remember($cacheKey, 120, function() use ($user, $isApprover) {
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
        
        $recentPOs = PurchaseOrder::with(['supplier:id,name', 'purchaseOrderDetails:item_amount,purchase_order_id'])
            ->select('id', 'doc_num', 'status', 'supplier_id', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($po) {
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
        
        $recentApprovals = PurchaseOrderApproval::with([
            'purchaseOrder:id,doc_num,supplier_id',
            'purchaseOrder.supplier:id,name',
            'approval_level:id,name'
        ])
            ->select('id', 'purchase_order_id', 'approval_level_id', 'status', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($approval) {
                return [
                    'id' => $approval->id,
                    'po_doc_num' => $approval->purchaseOrder->doc_num ?? 'N/A',
                    'supplier' => $approval->purchaseOrder->supplier->name ?? 'N/A',
                    'level' => $approval->approval_level->name ?? 'N/A',
                    'status' => $approval->status,
                    'created_at' => $approval->created_at->format('Y-m-d H:i'),
                    'url' => $approval->purchaseOrder ? route('procurement.po.show', $approval->purchaseOrder) : '#',
                ];
            });
        
        $pendingApprovalsForUser = [];
        if ($isApprover) {
            $pendingApprovalsForUser = PurchaseOrderApproval::with([
                'purchaseOrder:id,doc_num,supplier_id',
                'purchaseOrder.supplier:id,name',
                'approval_level:id,name'
            ])
                ->where('status', 'pending')
                ->whereHas('approval_level.approvers', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($approval) {
                    return [
                        'id' => $approval->id,
                        'po_doc_num' => $approval->purchaseOrder->doc_num ?? 'N/A',
                        'supplier' => $approval->purchaseOrder->supplier->name ?? 'N/A',
                        'level' => $approval->approval_level->name ?? 'N/A',
                        'created_at' => $approval->created_at->format('Y-m-d H:i'),
                        'url' => $approval->purchaseOrder ? route('procurement.po.show', $approval->purchaseOrder) : '#',
                    ];
                });
        }
        
        return [
            'recent_prs' => $recentPRs,
            'recent_pos' => $recentPOs,
            'recent_approvals' => $recentApprovals,
            'pending_approvals' => $pendingApprovalsForUser,
        ];
    });
    
    return response()->json($data);
}
```

#### Fix 3: Optimize Comment Count Polling

**Option A: Increase Interval** (Quick Fix)
- Change from 30 seconds to 60-120 seconds

**Option B: Remove Polling on Static Pages** (Better)
- Only load comment counts on page load
- Add manual refresh button
- Use real-time updates only when comment modal is open

**Option C: Cache Comment Counts** (Best)
```php
// app/Http/Controllers/CommentController.php
public function getCommentCounts(Request $request, $type, $id)
{
    $model = $this->getModel($type, $id);
    
    if (!$model) {
        return response()->json(['error' => 'Resource not found'], 404);
    }
    
    // Cache for 30 seconds
    $cacheKey = "comment_counts_{$type}_{$id}";
    
    return Cache::remember($cacheKey, 30, function() use ($model) {
        $headerCount = Comment::where('commentable_type', get_class($model))
            ->where('commentable_id', $model->id)
            ->whereNull('line_item_id')
            ->whereNull('parent_id')
            ->count();
        
        $lineItemCounts = Comment::where('commentable_type', get_class($model))
            ->where('commentable_id', $model->id)
            ->whereNotNull('line_item_id')
            ->whereNull('parent_id')
            ->selectRaw('line_item_id, COUNT(*) as count')
            ->groupBy('line_item_id')
            ->pluck('count', 'line_item_id');
        
        return [
            'header' => $headerCount,
            'line_items' => $lineItemCounts
        ];
    });
}
```

**Clear cache when comment is added/updated/deleted**:
```php
// In CommentController::store(), update(), destroy()
Cache::forget("comment_counts_{$type}_{$id}");
```

---

### 2.2 Database Indexes (Create Migration)

Create a new migration file:

```php
// database/migrations/YYYY_MM_DD_HHMMSS_add_performance_indexes.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Purchase Requests Indexes
        Schema::table('purchase_requests', function (Blueprint $table) {
            if (!$this->indexExists('purchase_requests', 'idx_pr_status_date')) {
                $table->index(['pr_status', 'generated_date'], 'idx_pr_status_date');
            }
            if (!$this->indexExists('purchase_requests', 'idx_pr_created_at')) {
                $table->index('created_at', 'idx_pr_created_at');
            }
            if (!$this->indexExists('purchase_requests', 'idx_pr_dept_status')) {
                $table->index(['dept_name', 'pr_status'], 'idx_pr_dept_status');
            }
        });
        
        // Purchase Orders Indexes
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!$this->indexExists('purchase_orders', 'idx_po_status_date')) {
                $table->index(['status', 'create_date'], 'idx_po_status_date');
            }
            if (!$this->indexExists('purchase_orders', 'idx_po_created_at')) {
                $table->index('created_at', 'idx_po_created_at');
            }
            if (!$this->indexExists('purchase_orders', 'idx_po_supplier_date')) {
                $table->index(['supplier_id', 'doc_date'], 'idx_po_supplier_date');
            }
        });
        
        // Purchase Order Approvals Indexes
        Schema::table('purchase_order_approvals', function (Blueprint $table) {
            if (!$this->indexExists('purchase_order_approvals', 'idx_poa_status_level')) {
                $table->index(['status', 'approval_level_id'], 'idx_poa_status_level');
            }
            if (!$this->indexExists('purchase_order_approvals', 'idx_poa_created_at')) {
                $table->index('created_at', 'idx_poa_created_at');
            }
        });
        
        // Purchase Order Details Indexes
        Schema::table('purchase_order_details', function (Blueprint $table) {
            if (!$this->indexExists('purchase_order_details', 'idx_pod_po_id')) {
                $table->index('purchase_order_id', 'idx_pod_po_id');
            }
        });
        
        // Partial index for approved_at (MySQL 8.0+)
        // Note: MySQL doesn't support partial indexes, use regular index instead
        Schema::table('purchase_order_approvals', function (Blueprint $table) {
            if (!$this->indexExists('purchase_order_approvals', 'idx_poa_approved_at')) {
                $table->index('approved_at', 'idx_poa_approved_at');
            }
        });
        
        // Notifications Indexes
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                if (!$this->indexExists('notifications', 'idx_notifications_created_at')) {
                    $table->index('created_at', 'idx_notifications_created_at');
                }
            });
            
            // Composite index for read status (if using Laravel notifications)
            DB::statement('CREATE INDEX idx_notifications_user_read ON notifications(notifiable_id, read_at) WHERE notifiable_type = "App\\\Models\\\User"');
        }
        
        // Comments Indexes
        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                if (!$this->indexExists('comments', 'idx_comments_commentable')) {
                    $table->index(['commentable_type', 'commentable_id'], 'idx_comments_commentable');
                }
                if (!$this->indexExists('comments', 'idx_comments_line_item')) {
                    $table->index('line_item_id', 'idx_comments_line_item');
                }
            });
        }
    }
    
    public function down()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropIndex('idx_pr_status_date');
            $table->dropIndex('idx_pr_created_at');
            $table->dropIndex('idx_pr_dept_status');
        });
        
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropIndex('idx_po_status_date');
            $table->dropIndex('idx_po_created_at');
            $table->dropIndex('idx_po_supplier_date');
        });
        
        Schema::table('purchase_order_approvals', function (Blueprint $table) {
            $table->dropIndex('idx_poa_status_level');
            $table->dropIndex('idx_poa_created_at');
            $table->dropIndex('idx_poa_approved_at');
        });
        
        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->dropIndex('idx_pod_po_id');
        });
        
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->dropIndex('idx_notifications_created_at');
            });
            DB::statement('DROP INDEX idx_notifications_user_read ON notifications');
        }
        
        if (Schema::hasTable('comments')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropIndex('idx_comments_commentable');
                $table->dropIndex('idx_comments_line_item');
            });
        }
    }
    
    private function indexExists($table, $index)
    {
        $connection = Schema::getConnection();
        $database = $connection->getDatabaseName();
        $result = $connection->select(
            "SELECT COUNT(*) as count FROM information_schema.statistics 
             WHERE table_schema = ? AND table_name = ? AND index_name = ?",
            [$database, $table, $index]
        );
        return $result[0]->count > 0;
    }
};
```

---

### 2.3 Optimize Active Suppliers Query

```php
// Instead of:
$activeSuppliers = Supplier::whereHas('purchaseOrders')->count();

// Use:
$activeSuppliers = DB::table('suppliers')
    ->join('purchase_orders', 'suppliers.id', '=', 'purchase_orders.supplier_id')
    ->distinct()
    ->count('suppliers.id');
```

---

### 2.4 Add Query Eager Loading Constraints

When loading relationships, only select needed columns:

```php
// Before:
$recentPOs = PurchaseOrder::with(['supplier', 'purchaseOrderDetails'])->get();

// After:
$recentPOs = PurchaseOrder::with([
    'supplier:id,name', // Only select id and name
    'purchaseOrderDetails:id,purchase_order_id,item_amount' // Only select needed columns
])
->select('id', 'doc_num', 'status', 'supplier_id', 'created_at') // Only select needed columns
->get();
```

---

## 3. Performance Monitoring

### 3.1 Add Query Logging (Development)

Add to `AppServiceProvider`:

```php
use Illuminate\Support\Facades\DB;

public function boot()
{
    if (app()->environment('local')) {
        DB::listen(function ($query) {
            if ($query->time > 1000) { // Log queries > 1 second
                \Log::warning('Slow Query Detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms'
                ]);
            }
        });
    }
}
```

### 3.2 Add Response Time Headers

Create middleware to log slow requests:

```php
// app/Http/Middleware/LogSlowRequests.php
public function handle($request, Closure $next)
{
    $start = microtime(true);
    $response = $next($request);
    $duration = (microtime(true) - $start) * 1000; // Convert to milliseconds
    
    if ($duration > 5000) { // Log requests > 5 seconds
        \Log::warning('Slow Request', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'duration' => round($duration, 2) . 'ms'
        ]);
    }
    
    return $response->header('X-Response-Time', round($duration, 2) . 'ms');
}
```

---

## 4. Implementation Priority

### Phase 1: Immediate (Do Today)
1. ✅ Add caching to dashboard activity endpoint
2. ✅ Optimize notification polling (increase interval or use smart polling)
3. ✅ Add database indexes migration
4. ✅ Cache comment counts

### Phase 2: This Week
1. Optimize query eager loading (select only needed columns)
2. Add query logging for slow queries
3. Optimize active suppliers query
4. Add response time headers

### Phase 3: Next Sprint
1. Implement Server-Sent Events or WebSockets for real-time notifications
2. Add Redis for caching (instead of file-based)
3. Implement database query result caching
4. Add monitoring dashboard for slow queries

---

## 5. Expected Impact

### Before Optimization:
- Notification API: 2 requests/minute per user = 120 requests/hour per user
- Dashboard Activity: 1 request per page load (no cache)
- Comment Counts: 2 requests/minute per user viewing detail page
- **Total**: ~200+ requests/hour per active user

### After Optimization:
- Notification API: 0.5-1 requests/minute per user = 30-60 requests/hour per user (60-75% reduction)
- Dashboard Activity: 1 request per page load, cached for 2 minutes (50% reduction on refreshes)
- Comment Counts: 1 request per 2 minutes per user = 30 requests/hour per user (75% reduction)
- **Total**: ~60-90 requests/hour per active user (55-70% reduction)

### Database Query Performance:
- Indexed queries: 50-90% faster
- Cached queries: 95-99% faster (no database hit)
- Overall: 60-80% reduction in database load

---

## 6. Testing Checklist

After implementing optimizations:

- [ ] Test notification polling with Page Visibility API
- [ ] Verify dashboard activity caching works
- [ ] Test comment count caching and cache invalidation
- [ ] Verify all database indexes are created
- [ ] Test slow query logging
- [ ] Load test with multiple concurrent users
- [ ] Monitor server response times
- [ ] Check database query performance
- [ ] Verify cache hit rates

---

**Document Version**: 1.0  
**Last Updated**: 2025-01-27  
**Next Review**: After implementing Phase 1 optimizations
