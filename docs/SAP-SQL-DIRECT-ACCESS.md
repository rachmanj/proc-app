# SAP B1 Direct SQL Server Access

## Date: 2025-11-13

## Updated: 2025-11-26 (v2.2 - Identity Tracking & Duplicate Prevention)

## Overview

To overcome OData limitations (field name mismatches, UDFs not exposed, complex joins), we've implemented direct SQL Server access to execute the exact SQL query from `list_ITO.sql`.

## Configuration

Add these environment variables to your `.env` file:

```env
# SAP SQL Server Direct Access
SAP_SQL_HOST=arkasrv2
SAP_SQL_PORT=1433
SAP_SQL_DATABASE=your_sap_database_name
SAP_SQL_USERNAME=your_sql_username
SAP_SQL_PASSWORD=your_sql_password
```

**Note**: If not specified, it will fall back to using `SAP_SERVER_URL`, `SAP_DB_NAME`, `SAP_USER`, and `SAP_PASSWORD` from the existing SAP configuration.

## Database Connection

The connection is configured in `config/database.php` as `sap_sql`:

```php
'sap_sql' => [
    'driver' => 'sqlsrv',
    'host' => env('SAP_SQL_HOST', env('SAP_SERVER_URL')),
    'port' => env('SAP_SQL_PORT', '1433'),
    'database' => env('SAP_SQL_DATABASE', env('SAP_DB_NAME')),
    'username' => env('SAP_SQL_USERNAME', env('SAP_USER')),
    'password' => env('SAP_SQL_PASSWORD', env('SAP_PASSWORD')),
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'options' => [
        'TrustServerCertificate' => true, // For development
    ],
],
```

## Implementation

### Service Method

`SapService::executeItoSqlQuery($startDate, $endDate)` executes the exact SQL query from `list_ITO.sql`:

- Uses parameterized queries for safety
- Executes on the `sap_sql` connection
- Returns results matching the SQL query structure exactly
- Includes all filters: `CreateDate`, `U_MIS_TransferType = 'OUT'`, warehouse join condition

### Sync Job Priority

The `SyncSapItoDocumentsJob` now tries methods in this order:

1. **SQL Server Direct Query** (new, most accurate)
2. OData Entity Query (fallback)
3. Query Execution via Service Layer (fallback)

## Benefits

✅ **100% Accuracy**: Matches SQL Query 5 results exactly  
✅ **All Filters Work**: `CreateDate`, `U_MIS_TransferType`, warehouse join  
✅ **Complete Data**: All fields from SQL query available  
✅ **No Field Mapping Issues**: Direct SQL field names  

## Requirements

- PHP `sqlsrv` extension installed
- Network access to SAP SQL Server
- SQL Server credentials with read access to `OWTR`, `WTR1`, `OITW`, etc.

## Testing

Test the SQL connection:

```bash
php artisan tinker
```

```php
DB::connection('sap_sql')->select('SELECT TOP 1 * FROM OWTR');
```

Test the sync:

```bash
php artisan sap:test-sync 2025-11-01 2025-11-12 --sync
```

## Security Considerations

- Use read-only SQL user if possible
- Store credentials securely in `.env` (never commit)
- Consider using encrypted connections in production
- Review SQL user permissions regularly

## Related Files

- `config/database.php` - Database connection configuration
- `app/Services/SapService.php` - SQL query execution and data mapping methods
  - `executeItoSqlQuery($startDate, $endDate)` - ITO document sync
  - `executePoSqlQuery($startDate, $endDate)` - PO data sync (v2.2)
  - `executePrSqlQuery($startDate, $endDate)` - PR data sync (v2.2)
  - `mapPoResultToModel($row)` - PO data mapping (v2.2)
  - `mapPrResultToModel($row)` - PR data mapping (v2.2)
- `app/Jobs/SyncSapItoDocumentsJob.php` - Updated to use SQL first
- `app/Http/Controllers/Master/SyncWithSapController.php` - Consolidated sync controller (v2.2)
- `database/list_ITO.sql` - Source SQL query for ITO documents
- `database/list_po.sql` - Source SQL query for PO data (v2.2)
- `database/list_pr_generated.sql` - Source SQL query for PR data (v2.2)
- `app/Models/SyncLog.php` - Sync operation logging model (v2.2)
- `database/migrations/*_create_sync_logs_table.php` - Sync logs table migration (v2.2)

## v2.2 Enhancements

### PR and PO Data Synchronization

The SAP B1 Direct SQL Server access has been extended to support PR and PO data synchronization:

- **Consolidated Sync Interface**: Single page (`/master/sync-with-sap`) for both PR and PO sync operations
- **Date Range Selection**: TODAY, YESTERDAY, and CUSTOM date range options with UTC+8 timezone support
- **Auto-Conversion**: Automatic conversion from temporary tables (`pr_temps`, `po_temps`) to main tables after successful sync
- **Sync Logging**: All sync operations are logged in `sync_logs` table with:
  - Sync status (success, failed, partial)
  - Conversion status (success, failed, skipped)
  - Record counts (synced, created, skipped)
  - Error messages for troubleshooting
- **Permission-Based Access**: 
  - `sync-custom-date`: Controls access to custom date range selection
  - `impor-sap-data`: Controls visibility of PR Import and PO Import menu items

### Usage

1. Navigate to **Sync Data > Sync With SAP** in the main menu
2. Select PR or PO tab
3. Choose date range (TODAY, YESTERDAY, or CUSTOM if permitted)
4. Click "Sync PR Data" or "Sync PO Data"
5. System automatically:
   - Queries SAP B1 SQL Server
   - Populates temporary tables with SAP line identifiers
   - Converts to main tables using identity-based deduplication
   - Logs the operation
   - Displays results

## v2.2 Identity Tracking & Duplicate Prevention (2025-11-26)

### Problem Solved

During SAP sync operations, duplicate PR and PO detail rows were being created when:
- The same data was synced multiple times
- SAP returned seemingly identical rows that differed only in metadata not captured by the application
- This caused inconsistencies between SAP exports and application data

### Solution: SAP Line Identity Tracking

Implemented identity tracking using SAP's native line identifiers to prevent duplicate detail rows:

**For PO Data:**
- SQL query (`database/list_po.sql`) now includes: `B.DocEntry [sap_doc_entry]`, `B.LineNum [sap_line_num]`, `B.VisOrder [sap_vis_order]`
- `po_temps` and `purchase_order_details` tables store these identifiers
- Unique constraint: `(purchase_order_id, sap_doc_entry, sap_line_num)`
- Fallback unique constraint: `(purchase_order_id, line_identity)` for Excel imports

**For PR Data:**
- SQL query (`database/list_pr_generated.sql`) now includes: `A.DocEntry [sap_doc_entry]`, `B.LineNum [sap_line_num]`, `B.VisOrder [sap_vis_order]`
- `pr_temps` and `purchase_request_details` tables store these identifiers
- Unique constraint: `(purchase_request_id, sap_doc_entry, sap_line_num)`
- Fallback unique constraint: `(purchase_request_id, line_identity)` for Excel imports

### Implementation Details

**Database Changes:**
- Migration: `2025_11_25_081404_add_sap_line_identity_to_po_tables.php`
- Migration: `2025_11_26_022512_add_sap_line_identity_to_pr_tables.php`
- Added columns: `sap_doc_entry`, `sap_line_num`, `sap_vis_order`, `line_identity` (hash for Excel imports)

**Service Updates:**
- `SapService::mapPoResultToModel()` and `mapPrResultToModel()` now include identity fields
- `POTempImport` and `PRTempImport` handle identity fields (nullable for Excel imports)

**Conversion Logic:**
- `SyncWithSapController::upsertPurchaseOrderDetail()` and `upsertPurchaseRequestDetail()` use identity-based unique keys
- Uses SAP IDs when available, falls back to hash-based `line_identity` for Excel imports
- `updateOrCreate` ensures idempotent sync operations

**Benefits:**
- ✅ Prevents duplicate detail rows during sync operations
- ✅ Ensures consistency between SAP exports and application data
- ✅ Works with both SAP sync and Excel imports (with fallback)
- ✅ Database-level enforcement via unique constraints
- ✅ Idempotent sync operations (safe to re-run)

### Debugging Tools

**Artisan Command:**
```bash
php artisan sap:dump-po --start=YYYY-MM-DD --end=YYYY-MM-DD [--po=PO_NUMBER] [--limit=N] [--path=custom/path.json] [--no-file]
```

This command allows direct inspection of raw SAP PO data before conversion, useful for:
- Validating SQL query results
- Analyzing data structure
- Debugging duplicate issues
- Comparing with SAP exports

