**Purpose**: AI's persistent knowledge base for project context and learnings
**Last Updated**: 2025-07-08

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

### PO Attachment Display Fix (2025-07-08) ✅ COMPLETE

-   **Challenge**: PO attachments not displaying in approval page on local development environment
-   **Solution**: Modified POController and show.blade.php to display attachment metadata without checking for local file existence
-   **Key Learning**: For development environments, prioritize displaying metadata over perfect file simulation
-   **Impact**: Improved development workflow by allowing attachment testing without needing production files locally
