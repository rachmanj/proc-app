**Purpose**: Record technical decisions and rationale for future reference
**Last Updated**: 2025-11-26

# Technical Decision Records

## Decision Template

Decision: [Title] - [YYYY-MM-DD]

**Context**: [What situation led to this decision?]

**Options Considered**:

1. **Option A**: [Description]
    - ✅ Pros: [Benefits]
    - ❌ Cons: [Drawbacks]
2. **Option B**: [Description]
    - ✅ Pros: [Benefits]
    - ❌ Cons: [Drawbacks]

**Decision**: [What we chose]

**Rationale**: [Why we chose this option]

**Implementation**: [How this affects the codebase]

**Review Date**: [When to revisit this decision]

---

## Recent Decisions

Decision: Performance Optimization Strategy - 2025-01-27

**Context**: Server log analysis revealed frequent polling (every 30 seconds) causing high API request volume (~200+ requests/hour per user) and slow queries (29 seconds to 1m 31s). Application needed optimization to reduce server load and improve response times.

**Options Considered**:

1. **Option A**: Increase polling intervals only

    - ✅ Pros: Quick fix, minimal code changes
    - ❌ Cons: Doesn't address slow queries, still unnecessary polling when tab inactive, poor user experience

2. **Option B**: Comprehensive optimization with caching, smart polling, and database indexes

    - ✅ Pros: Addresses root causes, significant performance gains (70-85% reduction), better UX with Page Visibility API
    - ❌ Cons: More implementation effort, requires migration

3. **Option C**: Server-Sent Events/WebSockets for real-time updates
    - ✅ Pros: Best user experience, no polling needed
    - ❌ Cons: Requires infrastructure changes, more complex, overkill for current needs

**Decision**: Implemented Option B - Comprehensive optimization with multiple strategies

**Rationale**:

-   Page Visibility API provides smart polling without infrastructure changes
-   Caching expensive queries provides immediate 50-90% performance gains
-   Database indexes are essential for query performance and should be added regardless
-   Achieves significant improvements with manageable implementation effort
-   Can upgrade to SSE/WebSockets later if needed

**Implementation**:

-   Dashboard activity endpoint: 2-minute cache with optimized eager loading
-   Notification polling: Page Visibility API (30s active, 5min inactive) + 30s backend cache
-   Database indexes: 14 indexes added via migration (PR, PO, Approvals, Details, Notifications, Comments)
-   Comment counts: Removed automatic polling, load on page load only

**Review Date**: 2025-04-27 (3 months) - Evaluate if SSE/WebSockets needed, consider Redis for caching

---

Decision: Implement DataTables for Item Price Search - 2025-08-03

**Context**: The consignment item price search page needed enhanced functionality for users to efficiently find, sort, and export item price data. The standard Laravel pagination was limiting user experience.

**Options Considered**:

1. **Option A**: Custom JavaScript filtering and sorting

    - ✅ Pros: Complete control over implementation, no additional libraries
    - ❌ Cons: Time-consuming development, maintenance burden, limited features

2. **Option B**: DataTables integration

    - ✅ Pros: Rich feature set (sorting, filtering, export), responsive design, well-documented
    - ❌ Cons: Additional JavaScript dependencies, learning curve for customization

3. **Option C**: Server-side filtering with AJAX
    - ✅ Pros: Better performance for large datasets, reduced client-side processing
    - ❌ Cons: Complex implementation, more backend code, less immediate user feedback

**Decision**: Implemented Option B - DataTables integration for the item price search functionality

**Rationale**:

-   Provides immediate improvement to user experience with minimal development effort
-   Built-in export functionality (CSV, Excel, PDF) meets business requirements
-   Responsive design works well across different devices
-   Existing AdminLTE theme already includes DataTables, reducing integration effort

**Implementation**:

-   Added DataTables CSS and JS resources to the search.blade.php view
-   Modified the controller to return all results instead of paginated data
-   Implemented client-side filtering with form integration
-   Added part number search capability for more comprehensive filtering
-   Configured responsive display and export options

**Review Date**: 2025-11-03 (3 months)

Decision: Display PO Attachments Without Local File Existence Check - 2025-07-08

**Context**: When developing locally, PO attachments were not displaying in the approval page because the physical files exist only on the production server. This made it difficult to test and develop the approval workflow on local environments.

**Options Considered**:

1. **Option A**: Create dummy files locally to match production

    - ✅ Pros: Realistic testing environment
    - ❌ Cons: Time-consuming, requires maintaining duplicate files, storage overhead

2. **Option B**: Skip file existence check and display attachment metadata regardless

    - ✅ Pros: Simple implementation, works immediately, no storage overhead
    - ❌ Cons: View buttons will lead to 404 errors when clicked locally

3. **Option C**: Create a mock file service that returns placeholders
    - ✅ Pros: More realistic end-to-end testing
    - ❌ Cons: Complex implementation, maintenance overhead

**Decision**: Implemented Option B - Skip file existence check and display attachment metadata regardless of local file availability

**Rationale**:

-   Development speed is prioritized over perfect local simulation
-   Metadata display is sufficient for most development and testing needs
-   Simple implementation with minimal code changes
-   No need to duplicate potentially large files across environments

**Implementation**:

-   Modified `POController.php` in Approvals namespace to remove file existence check
-   Updated `show.blade.php` view to always display attachment metadata
-   View buttons still present but will lead to 404 errors when files don't exist locally

**Review Date**: 2025-10-08 (3 months)

---

Decision: SAP B1 Direct SQL Server Sync Implementation - 2025-11-17

**Context**: The system needed to synchronize PR and PO data directly from SAP B1 SQL Server to eliminate manual Excel imports. The existing Excel import workflow required users to export data from SAP, format it, and manually import it, which was time-consuming and error-prone.

**Options Considered**:

1. **Option A**: Continue with Excel import workflow
    - ✅ Pros: No new infrastructure needed, users familiar with process
    - ❌ Cons: Manual, time-consuming, error-prone, requires data formatting

2. **Option B**: Direct SQL Server connection with consolidated sync interface
    - ✅ Pros: Automated, eliminates manual steps, real-time data, consolidated UI, auto-conversion
    - ❌ Cons: Requires SQL Server access, additional database connection, more complex implementation

3. **Option C**: OData API integration
    - ✅ Pros: Standard API approach, no direct database access needed
    - ❌ Cons: Limited by OData field mapping issues, UDFs not exposed, complex joins difficult

**Decision**: Implemented Option B - Direct SQL Server connection with consolidated sync interface

**Rationale**:
- Direct SQL access overcomes OData limitations (field name mismatches, UDFs not exposed, complex joins)
- Consolidated interface provides better UX with unified PR and PO sync in one place
- Auto-conversion feature eliminates manual step of converting temp tables to main tables
- Detailed logging provides audit trail and troubleshooting capabilities
- Permission-based access control allows fine-grained security (custom date ranges, import menu visibility)

**Implementation**:
- Added `sap_sql` database connection in `config/database.php` for SQL Server access
- Created `SapService` to handle SQL queries and data mapping
- Implemented `SyncWithSapController` with consolidated sync interface
- Added `sync_logs` table for tracking sync operations
- Created tabbed UI with PR and PO sync panels
- Implemented date range selection (TODAY, YESTERDAY, CUSTOM) with UTC+8 timezone support
- Added auto-conversion from temp tables to main tables after successful sync
- Created new permissions: `sync-custom-date` and `impor-sap-data`
- Updated menu structure: "Master" renamed to "Sync Data" with "Sync With SAP" as primary option
- Removed individual "Sync from SAP" buttons from PR and PO import pages

**Review Date**: 2026-02-17 (3 months)

---

Decision: SAP Line Identity Tracking for Duplicate Prevention - 2025-11-26

**Context**: During SAP sync operations, duplicate PO and PR detail rows were being created when the same data was synced multiple times or when SAP returned seemingly identical rows that differed only in metadata not captured by the application. This caused data integrity issues and inconsistencies between SAP exports and application data.

**Options Considered**:

1. **Option A**: Use business logic fields (item_code, qty, description) for deduplication
    - ✅ Pros: No schema changes needed, works with Excel imports
    - ❌ Cons: Cannot distinguish between legitimate duplicate items, fails when SAP returns identical rows with different line numbers

2. **Option B**: Add SAP line identifiers (DocEntry, LineNum, VisOrder) and use them for deduplication
    - ✅ Pros: Matches SAP's native line identification, prevents true duplicates, works with both SAP sync and Excel imports (with fallback)
    - ❌ Cons: Requires schema changes, SQL queries need updates, migration needed

3. **Option C**: Aggregate identical lines during conversion
    - ✅ Pros: Reduces data volume
    - ❌ Cons: Loses line-level detail, may hide legitimate separate lines, complex aggregation logic

**Decision**: Implemented Option B - SAP line identity tracking with fallback to hash-based identity

**Rationale**:
- SAP's native line identifiers (DocEntry, LineNum, VisOrder) provide unique identification for each detail line
- Allows precise duplicate detection matching SAP's own line numbering system
- Fallback to hash-based `line_identity` ensures Excel imports (which may not have SAP IDs) still work
- Unique database constraints enforce data integrity at the database level
- `updateOrCreate` logic in conversion ensures idempotent sync operations

**Implementation**:
- Updated `database/list_po.sql` and `database/list_pr_generated.sql` to include SAP line identifiers
- Added migrations to add `sap_doc_entry`, `sap_line_num`, `sap_vis_order`, and `line_identity` columns to:
  - `po_temps` and `purchase_order_details` tables
  - `pr_temps` and `purchase_request_details` tables
- Added unique constraints: `(purchase_order_id, sap_doc_entry, sap_line_num)` and `(purchase_order_id, line_identity)`
- Updated `SapService::mapPoResultToModel()` and `mapPrResultToModel()` to include identity fields
- Modified `POTempImport` and `PRTempImport` to handle identity fields (nullable for Excel imports)
- Updated conversion logic in `SyncWithSapController` and `DailyPRController` to use `updateOrCreate` with identity-based unique keys
- Created `upsertPurchaseOrderDetail()` and `upsertPurchaseRequestDetail()` methods that use SAP IDs when available, fallback to hash when not

**Review Date**: 2026-02-26 (3 months)

---

Decision: PR Detail Identity Reconciliation During SAP Sync - 2025-11-27

**Context**: After enabling SAP line identity tracking, some PR detail rows created via Excel import retained placeholder `sap_line_num` values (often `0`). When SAP sync reprocessed the same lines with correct line numbers, the converter attempted to insert new rows keyed by the SAP identifiers, causing `duplicate entry ... line_identity` errors and blocking the sync pipeline.

**Options Considered**:

1. **Option A**: Drop the `(purchase_request_id, line_identity)` unique constraint
    - ✅ Pros: Eliminates the immediate database error
    - ❌ Cons: Reintroduces duplicate rows when Excel imports overlap with SAP sync

2. **Option B**: Teach the converter to reconcile records created via hash fallback before inserting with SAP identifiers
    - ✅ Pros: Preserves both unique constraints, keeps Excel import compatibility, and auto-heals legacy rows with incorrect `sap_line_num`
    - ❌ Cons: Requires additional queries during conversion

**Decision**: Implemented Option B to ensure SAP-sourced rows first try to match existing records by `line_identity` before creating new entries keyed by SAP DocEntry/LineNum.

**Implementation**:
- Updated `SyncWithSapController::upsertPurchaseRequestDetail()` and `DailyPRController::upsertPurchaseRequestDetail()` to:
  - Build a shared payload containing SAP identifiers and hash-based `line_identity`
  - When SAP DocEntry/LineNum are present, update the record with the same `line_identity` if it exists (covering Excel-imported rows and SAP rows with shifting line numbers)
  - Fall back to an `updateOrCreate` keyed by SAP identifiers only after the hash lookup fails
  - Continue using the hash-only lookup when SAP identifiers are missing
- Keeps database constraints unchanged and ensures idempotent sync behavior even when SAP renumbers lines.

**Review Date**: 2026-02-27 (3 months)
