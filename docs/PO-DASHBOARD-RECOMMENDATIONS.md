# PO Dashboard Content Recommendations

**Date**: 2025-11-04  
**Page**: `/procurement/po` (Dashboard)

## Current State
- PO dashboard page is currently empty (blank card)
- PR dashboard has a similar structure with "General Info" table showing counts by project code
- PO data has rich information: status, project_code, unit_no, supplier_id, and values

## Recommended Dashboard Content

### 1. **Summary Metrics Cards** (Top Row)
Similar to main dashboard but PO-specific:
- **Total POs** (all statuses)
- **Draft POs** (not yet submitted)
- **Submitted POs** (awaiting approval)
- **Approved POs** (completed)
- **Total PO Value** (sum of all PO values)
- **Monthly PO Value** (current month)
- **Active Suppliers** (suppliers with POs)

### 2. **General Info Table** (Similar to PR Dashboard)
Breakdown by **Project Code**:
- Total PO Count by project
- Draft PO Count by project
- Submitted PO Count by project
- Total PO Value by project

### 3. **Status Distribution Chart**
- Pie/Donut chart showing PO distribution by status
- Visual representation of draft vs submitted vs approved

### 4. **Supplier Overview**
- Top 10 suppliers by PO count
- Top 10 suppliers by PO value
- Total unique suppliers

### 5. **Unit-wise Breakdown** (Optional)
If unit_no is important:
- PO count by unit
- PO value by unit
- Top 10 units by PO activity

### 6. **Recent Activity**
- Recent POs created
- Recent PO status changes
- Recent approvals

### 7. **Quick Actions**
- Create New PO
- Search POs
- View All Draft POs
- View All Submitted POs

## Implementation Priority

### Phase 1: Essential (Immediate)
1. ✅ Summary Metrics Cards (Total, Draft, Submitted, Approved)
2. ✅ General Info Table (by Project Code)
3. ✅ Status Distribution Chart

### Phase 2: Enhanced (Next)
4. Supplier Overview
5. Recent Activity Widget
6. Quick Actions

### Phase 3: Advanced (Future)
7. Unit-wise Breakdown
8. Value Trend Charts
9. Approval Timeline

## Design Considerations
- Match PR dashboard style for consistency
- Use AdminLTE card components
- Responsive design (mobile-friendly)
- Use Chart.js for visualizations (already available)
- Load data via AJAX for better performance

## Data Requirements
- PO counts by status
- PO counts by project_code
- PO values (from purchase_order_details)
- Supplier statistics
- Recent PO activity

