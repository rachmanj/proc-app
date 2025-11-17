**Purpose**: AI's persistent knowledge base for project context and learnings
**Last Updated**: 2025-11-17

## Memory Maintenance Guidelines

### Structure Standards

-   **Entry Format**: `### Title (YYYY-MM-DD) ✅ STATUS`
-   **Required Fields**: Date, Challenge/Decision, Solution, Key Learning
-   **Length Limit**: 3-6 lines per entry (excluding sub-bullets)
-   **Status Indicators**: ✅ COMPLETE, ⚠️ PARTIAL, ❌ BLOCKED

### Content Guidelines

-   **Focus**: Architecture decisions, critical bugs, security fixes, major technical challenges
-   **Exclude**: Routine features, minor bug fixes, documentation updates
-   **Learning**: Each entry must include actionable learning or decision rationale
-   **Redundancy**: Remove duplicate information, consolidate similar issues

### File Management

-   **Archive Trigger**: When file exceeds 500 lines or 6 months old
-   **Archive Format**: `memory-YYYY-MM.md` (e.g., `memory-2025-01.md`)
-   **New File**: Start fresh with current date and carry forward only active decisions

---

## Project Memory Entries

### Comprehensive Architecture Documentation Update (2025-01-27) ✅ COMPLETE

-   **Challenge**: Architecture documentation needed comprehensive update to reflect current codebase state including all modules, models, routes, and workflows
-   **Solution**: Conducted comprehensive codebase analysis and updated docs/architecture.md with complete system structure, database schema details, workflow diagrams, observer patterns, and deployment information
-   **Key Learning**: Documentation should be treated as living documents that reflect current state, not intended state; comprehensive documentation helps AI assistants and developers understand system quickly
-   **Impact**: Architecture documentation now provides complete reference for all 8 major modules (Auth, PR, PO, Approval, Consignment, PO Service, Master Data, User Management) with full database schema and workflow diagrams

### DataTables Implementation for Item Price Search (2025-08-03) ✅ COMPLETE

-   **Challenge**: Need for advanced search, sorting, and export capabilities in consignment item price search
-   **Solution**: Implemented DataTables with client-side processing and integrated with existing search form
-   **Key Learning**: DataTables column indices must be updated when adding new columns to maintain proper filtering
-   **Impact**: Significantly improved user experience with interactive filtering, sorting, and data export options

### Consignment Middleware Fix (2025-08-02) ✅ COMPLETE

-   **Challenge**: "Target class [permission] does not exist" error when accessing /consignment page
-   **Solution**: Fixed middleware namespace in bootstrap/app.php from "Middlewares" (plural) to "Middleware" (singular)
-   **Key Learning**: Laravel middleware registration requires exact namespace matching
-   **Impact**: Enabled access to the consignment feature with proper permission checks

### PO Attachment Display Fix (2025-07-08) ✅ COMPLETE

-   **Challenge**: PO attachments not displaying in approval page on local development environment
-   **Solution**: Modified POController and show.blade.php to display attachment metadata without checking for local file existence
-   **Key Learning**: For development environments, prioritize displaying metadata over perfect file simulation
-   **Impact**: Improved development workflow by allowing attachment testing without needing production files locally

### SAP B1 Direct SQL Server Sync Implementation (2025-11-17) ✅ COMPLETE

-   **Challenge**: Manual Excel import workflow for PR and PO data from SAP B1 was time-consuming and error-prone
-   **Solution**: Implemented direct SQL Server connection with consolidated sync interface, auto-conversion, and detailed logging
-   **Key Learning**: Direct SQL access overcomes OData limitations; consolidated UI improves UX; auto-conversion eliminates manual steps
-   **Impact**: Eliminated manual Excel import process, reduced errors, improved data freshness, provided audit trail via sync_logs table
-   **Technical Details**: Created SapService for SQL queries, SyncWithSapController for consolidated interface, sync_logs table for tracking, permission-based access control
