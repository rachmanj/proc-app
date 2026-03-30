# Phase 3.6: Polish & Testing - Testing Documentation

**Date**: 2025-11-04  
**Status**: In Progress  
**Version**: 1.0

---

## 1. Test Credentials

**Login Credentials:**
- Username: `superadmin` | Password: `password`
- Username: `procmgr` | Password: `password`
- Username: `yuwana` | Password: `password`
- Username: `embang` | Password: `password`

---

## 2. Testing Checklist

### 2.1 Core Comments (Phase 3.1)

#### Basic Comment Functionality
- [ ] **Create Comment**
  - Navigate to PR detail page: `/procurement/pr/{id}`
  - Scroll to Comments section
  - Type comment in Quill editor
  - Click "Post Comment"
  - Verify comment appears in comments list
  - Verify comment count updates

- [ ] **Edit Comment**
  - Click Edit button on own comment
  - Modify comment content
  - Click "Update Comment"
  - Verify comment is updated
  - Verify "Edited" indicator (if applicable)

- [ ] **Delete Comment**
  - Click Delete button on own comment
  - Confirm deletion
  - Verify comment is removed
  - Verify comment count updates

- [ ] **Reply to Comment**
  - Click Reply button on a comment
  - Type reply in Quill editor
  - Click "Post Comment"
  - Verify reply appears nested under parent comment
  - Verify threaded display

#### Rich Text Editor
- [ ] **Formatting Options**
  - Test bold text
  - Test italic text
  - Test underline
  - Test headings (H1, H2, H3)
  - Test bullet lists
  - Test numbered lists
  - Test links
  - Test code blocks

- [ ] **Content Persistence**
  - Create comment with formatting
  - Reload page
  - Verify formatting is preserved

#### File Attachments
- [ ] **Upload Attachment**
  - Click "Attachments" button
  - Select file(s) (PDF, DOCX, XLSX, images, ZIP)
  - Verify file preview appears
  - Post comment
  - Verify attachment appears in comment
  - Verify attachment is downloadable

- [ ] **Multiple Attachments**
  - Upload multiple files in one comment
  - Verify all attachments appear
  - Verify all are downloadable

- [ ] **File Size Limit**
  - Try uploading file > 10MB
  - Verify error message

#### @Mentions
- [ ] **Autocomplete Dropdown**
  - Type `@` in comment editor
  - Verify dropdown appears with user list
  - Verify keyboard navigation (Arrow Up/Down)
  - Verify Enter key selects user
  - Verify Escape key closes dropdown

- [ ] **Mention Insertion**
  - Select user from dropdown
  - Verify mention is inserted with formatting
  - Post comment
  - Verify mention appears as styled link
  - Verify mention is clickable (if applicable)

- [ ] **Mention Parsing**
  - Create comment with multiple mentions
  - Verify all mentions are parsed correctly
  - Check backend: verify `comment_mentions` table has records

### 2.2 Line Item Comments (Phase 3.3)

#### Line Item Comment Indicators
- [ ] **Comment Button Display**
  - Navigate to PR/PO detail page
  - Verify comment button appears in each line item row
  - Verify button is clickable

- [ ] **Comment Count Badge**
  - Add comment to line item
  - Verify badge appears with count
  - Verify badge updates when comments added/removed
  - Verify badge hides when count is 0

#### Line Item Comment Modal
- [ ] **Modal Opens**
  - Click comment button on line item
  - Verify modal opens
  - Verify modal title shows line item info

- [ ] **View Line Item Comments**
  - Add comment to line item
  - Open modal
  - Verify comment appears in modal
  - Verify comment displays correctly

- [ ] **Add Line Item Comment**
  - Open line item comment modal
  - Type comment in editor
  - Click "Post Comment"
  - Verify comment is added
  - Verify comment appears in modal
  - Verify comment count badge updates

### 2.3 Activity Timeline (Phase 3.4)

#### Timeline Display
- [ ] **Activities Load**
  - Navigate to PR/PO detail page
  - Scroll to Activity Timeline section
  - Verify activities load
  - Verify activities are grouped by date

- [ ] **Activity Types**
  - Verify comment activities appear
  - Verify file upload activities appear
  - Verify file delete activities appear
  - Verify status change activities appear
  - Verify approval activities appear
  - Verify assignment activities appear
  - Verify follow activities appear

#### Filtering
- [ ] **Filter by Event Type**
  - Select event type from dropdown
  - Click "Filter"
  - Verify only matching activities shown
  - Click "Clear"
  - Verify all activities shown

- [ ] **Filter by User**
  - Select user from dropdown
  - Click "Filter"
  - Verify only activities by that user shown

- [ ] **Filter by Date Range**
  - Select date from "From" field
  - Select date to "To" field
  - Click "Filter"
  - Verify only activities in date range shown

### 2.4 Collaboration Features (Phase 3.5)

#### Follow/Unfollow
- [ ] **Follow PR/PO**
  - Click "Follow" button
  - Verify button changes to "Following" (with active state)
  - Verify follower count increases
  - Verify activity is logged

- [ ] **Unfollow PR/PO**
  - Click "Following" button
  - Verify button changes to "Follow"
  - Verify follower count decreases

- [ ] **Follow Status Persistence**
  - Follow a PR/PO
  - Reload page
  - Verify button still shows "Following"

#### Assignment System
- [ ] **Assign User**
  - Click "Assign" dropdown
  - Select user from dropdown
  - Add optional notes
  - Click "Assign"
  - Verify user appears in assigned users list
  - Verify user badge appears above comments
  - Verify activity is logged

- [ ] **Multiple Assignments**
  - Assign multiple users
  - Verify all appear in list
  - Verify all badges appear

- [ ] **Remove Assignment**
  - Click remove button on assigned user
  - Confirm removal
  - Verify user is removed from list
  - Verify badge disappears

- [ ] **Assignment Notes**
  - Assign user with notes
  - Verify notes appear in assignment list
  - Verify notes are visible to other users

---

## 3. Performance Testing

### 3.1 Query Optimization
- [ ] **Check N+1 Queries**
  - Use Laravel Debugbar or Telescope
  - Load PR detail page
  - Verify eager loading is used for:
    - Comments with user, replies, mentions, attachments
    - Assignments with assigned users
    - Followers
    - Activities with causer

- [ ] **Large Dataset Testing**
  - Test with PR/PO that has 50+ comments
  - Verify page loads in < 3 seconds
  - Verify comments load efficiently

### 3.2 JavaScript Performance
- [ ] **Editor Initialization**
  - Verify Quill editor loads quickly
  - Verify no console errors

- [ ] **AJAX Calls**
  - Verify all AJAX calls complete in < 1 second
  - Verify proper loading indicators

---

## 4. UI/UX Improvements Checklist

### 4.1 Visual Design
- [ ] **Spacing & Layout**
  - Verify consistent spacing between sections
  - Verify proper padding on cards
  - Verify comments section is readable

- [ ] **Colors & Styling**
  - Verify pinned comments have yellow border
  - Verify resolved comments have reduced opacity
  - Verify mention links are styled correctly
  - Verify activity timeline colors are distinct

- [ ] **Responsive Design**
  - Test on mobile viewport (< 768px)
  - Test on tablet viewport (768px - 1024px)
  - Test on desktop viewport (> 1024px)
  - Verify all features work on all sizes

### 4.2 User Experience
- [ ] **Loading States**
  - Verify loading indicators appear during AJAX calls
  - Verify "Loading comments..." message
  - Verify "Loading activity timeline..." message

- [ ] **Error Handling**
  - Test with invalid data
  - Verify error messages are user-friendly
  - Verify errors don't break UI

- [ ] **Accessibility**
  - Verify keyboard navigation works
  - Verify screen reader compatibility (if applicable)
  - Verify focus indicators are visible

---

## 5. Known Issues & Fixes

### Issue 1: Route Ordering
**Problem**: `/procurement/comments/users/search` was returning 404  
**Cause**: Route was defined after parameterized route `/{type}/{id}`  
**Fix**: Moved specific routes before parameterized routes  
**Status**: ✅ Fixed

### Issue 2: (Add more as found)

---

## 6. Browser Compatibility

Test on:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Edge (latest)
- [ ] Safari (latest, if available)

---

## 7. Performance Benchmarks

### Page Load Times
- PR Detail Page (no comments): < 1 second
- PR Detail Page (10 comments): < 2 seconds
- PR Detail Page (50 comments): < 3 seconds

### AJAX Response Times
- Load Comments: < 500ms
- Post Comment: < 1 second
- Load Activity Timeline: < 1 second
- Filter Activities: < 500ms

---

## 8. Security Testing

- [ ] **XSS Prevention**
  - Test with script tags in comments
  - Verify scripts are sanitized

- [ ] **CSRF Protection**
  - Verify all POST/DELETE requests have CSRF tokens
  - Verify tokens are validated

- [ ] **Authorization**
  - Verify users can only edit/delete their own comments
  - Verify users can only assign/unassign if they have permission

- [ ] **File Upload Security**
  - Verify file type validation
  - Verify file size limits
  - Verify malicious files are blocked

---

## 9. Integration Testing

- [ ] **Comments + Activity Timeline**
  - Post comment
  - Verify activity appears in timeline
  - Verify activity has correct details

- [ ] **Assignments + Activity Timeline**
  - Assign user
  - Verify activity appears in timeline

- [ ] **Follows + Activity Timeline**
  - Follow PR/PO
  - Verify activity appears in timeline

---

## 10. Documentation Updates

### Files to Update
- [ ] `docs/architecture.md` - Update with new models and relationships
- [ ] `docs/todo.md` - Update progress
- [ ] `MEMORY.md` - Add any important learnings
- [ ] `README.md` - Update feature list (if applicable)

---

## 11. User Training Materials

### Quick Start Guide
- [ ] Create guide for adding comments
- [ ] Create guide for @mentions
- [ ] Create guide for line item comments
- [ ] Create guide for assignments
- [ ] Create guide for following items

---

## 12. Deployment Checklist

### Pre-Deployment
- [ ] Run all migrations
- [ ] Clear route cache
- [ ] Clear config cache
- [ ] Clear view cache
- [ ] Test on staging environment

### Post-Deployment
- [ ] Verify all features work
- [ ] Check error logs
- [ ] Monitor performance
- [ ] Gather user feedback

---

**Last Updated**: 2025-11-04  
**Next Review**: After testing completion

