Keep your task management simple and focused on what you're actually working on:

```markdown
**Purpose**: Track current work and immediate priorities
**Last Updated**: 2025-07-08

## Task Management Guidelines

### Entry Format

Each task entry must follow this format:
[status] priority: task description [context] (completed: YYYY-MM-DD)

### Context Information

Include relevant context in brackets to help with future AI-assisted coding:

-   **Files**: `[src/components/Search.tsx:45]` - specific file and line numbers
-   **Functions**: `[handleSearch(), validateInput()]` - relevant function names
-   **APIs**: `[/api/jobs/search, POST /api/profile]` - API endpoints
-   **Database**: `[job_results table, profiles.skills column]` - tables/columns
-   **Error Messages**: `["Unexpected token '<'", "404 Page Not Found"]` - exact errors
-   **Dependencies**: `[blocked by auth system, needs API key]` - blockers

### Status Options

-   `[ ]` - pending/not started
-   `[WIP]` - work in progress
-   `[blocked]` - blocked by dependency
-   `[testing]` - testing in progress
-   `[done]` - completed (add completion date)

### Priority Levels

-   `P0` - Critical (app won't work without this)
-   `P1` - Important (significantly impacts user experience)
-   `P2` - Nice to have (improvements and polish)
-   `P3` - Future (ideas for later)

--- Example

# Current Tasks

## Working On Now

-   `[WIP] P1: Update project documentation to reflect current architecture [docs/architecture.md]`

## Up Next (This Week)

-   `[ ] P2: Improve error handling for attachment display [app/Http/Controllers/Approvals/POController.php]`
-   `[ ] P2: Add logging for attachment access attempts [app/Http/Controllers/Approvals/POController.php]`

## Blocked/Waiting

-   `[blocked] P3: Implement attachment preview for common file types [waiting for frontend library decision]`

## Recently Completed

-   `[done] P0: Fix PO attachment display in approval page [app/Http/Controllers/Approvals/POController.php, resources/views/approvals/po/show.blade.php] (completed: 2025-07-08)`
-   `[done] P1: Remove local file existence check for attachments [resources/views/approvals/po/show.blade.php] (completed: 2025-07-08)`

## Quick Notes

-   PO attachments now display in the approval page even if the physical files are only on the production server
-   The system now shows attachment metadata regardless of local file availability
-   This change helps with development on local environments while files exist only on production
```
