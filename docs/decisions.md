**Purpose**: Record technical decisions and rationale for future reference
**Last Updated**: 2025-11-17

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
