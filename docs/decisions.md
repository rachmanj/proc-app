**Purpose**: Record technical decisions and rationale for future reference
**Last Updated**: 2025-07-08

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
