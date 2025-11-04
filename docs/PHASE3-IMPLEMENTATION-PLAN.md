# Phase 3 Implementation Plan: Comments & Collaboration + Document Management

**Date**: 2025-11-04  
**Status**: Planning Phase  
**Priority**: P2 (Nice to have, but important for collaboration)

---

## Executive Summary

Phase 3 enhancements will transform the procurement system from a transaction-focused application into a collaborative platform. These features will enable real-time communication, better document management, and improved team collaboration throughout the PR/PO lifecycle.

---

## 1. Comments & Collaboration System

### 1.1 Current State Analysis

**What exists now:**
- ✅ Basic `notes` field in `purchase_order_approvals` table (single text field)
- ✅ `remarks` field in PRs and POs (single text field)
- ✅ `line_remarks` field in PR/PO line items
- ❌ No threaded comments
- ❌ No @mentions
- ❌ No file attachments in comments
- ❌ No activity timeline
- ❌ No collaboration features (assign, follow, watchlist)

**Limitations:**
- Notes are only visible during approval process
- No conversation history
- No way to tag specific users
- No way to discuss specific line items
- No notification system for comments

### 1.2 Proposed Features

#### A. Threaded Comments System

**1. Header-Level Comments**
- Comments on entire PR/PO documents
- Threaded replies (comment → reply → nested replies)
- Rich text editor (bold, italic, lists, links)
- File attachments per comment
- @mention users to notify them
- Edit/delete own comments
- Timestamps and user attribution

**2. Line Item Comments**
- Comments on specific PR/PO line items
- Visual indicator showing comment count on each line
- Click to view/expand comments
- Same features as header comments (threading, @mentions, attachments)

**3. Comment Features**
- **Rich Text Editing**: 
  - Bold, italic, underline
  - Bullet/numbered lists
  - Links
  - Code blocks (for technical discussions)
- **@Mentions**:
  - Autocomplete user search
  - Send notification to mentioned users
  - Highlight mentioned users in comments
- **File Attachments**:
  - Upload multiple files per comment
  - Support: PDF, DOCX, XLSX, images, ZIP
  - Preview attachments inline
  - Download attachments
- **Comment Actions**:
  - Edit (with "Edited" indicator)
  - Delete (soft delete with "Deleted by user" message)
  - Mark as resolved (for question/answer threads)
  - Pin important comments

#### B. Activity Timeline

**Unified Activity Feed** showing:
- Status changes (e.g., "PR moved from Draft to Submitted")
- Comments added
- Files uploaded/removed
- Approvals/rejections with notes
- Assignment changes
- Line item modifications
- User activity (who did what, when)

**Timeline Features:**
- Chronological view (newest first or oldest first)
- Filter by activity type (comments, approvals, files, etc.)
- Filter by user
- Search timeline
- Export timeline as PDF
- Real-time updates (via WebSockets or polling)

#### C. Collaboration Features

**1. Assignment System**
- Assign PRs/POs to team members
- Assign to multiple users (for collaboration)
- Assignment history tracking
- Visual indicators (badges, avatars) showing assigned users
- Dashboard widget: "Assigned to Me"
- Filter/search by assigned user

**2. Follow/Watchlist**
- "Follow" button on PR/PO pages
- Get notified of all changes on followed items
- Unfollow when no longer needed
- "My Watchlist" dashboard widget
- Notification preferences (email, in-app, or both)

**3. Team Collaboration**
- Share PR/PO with specific users (even if not assigned)
- Team mentions (@team-department, @team-procurement)
- Collaborative editing indicators
- "Who's viewing" indicator (real-time)

### 1.3 Database Design

#### New Tables

**1. `comments` table**
```sql
CREATE TABLE comments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    commentable_type VARCHAR(255), -- 'App\Models\PurchaseRequest' or 'App\Models\PurchaseOrder'
    commentable_id BIGINT UNSIGNED, -- PR/PO ID
    parent_id BIGINT UNSIGNED NULL, -- For threading (NULL = top-level comment)
    line_item_id BIGINT UNSIGNED NULL, -- For line item comments (NULL = header comment)
    user_id BIGINT UNSIGNED,
    content TEXT, -- Rich text content (HTML)
    content_plain TEXT, -- Plain text version for search
    is_resolved BOOLEAN DEFAULT FALSE,
    is_pinned BOOLEAN DEFAULT FALSE,
    is_deleted BOOLEAN DEFAULT FALSE,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_commentable (commentable_type, commentable_id),
    INDEX idx_parent (parent_id),
    INDEX idx_line_item (line_item_id),
    INDEX idx_user (user_id),
    INDEX idx_resolved (is_resolved),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**2. `comment_mentions` table**
```sql
CREATE TABLE comment_mentions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    comment_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    created_at TIMESTAMP,
    
    INDEX idx_comment (comment_id),
    INDEX idx_user (user_id),
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**3. `comment_attachments` table**
```sql
CREATE TABLE comment_attachments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    comment_id BIGINT UNSIGNED,
    file_name VARCHAR(255),
    file_path VARCHAR(500),
    file_type VARCHAR(100),
    file_size BIGINT UNSIGNED,
    mime_type VARCHAR(100),
    created_at TIMESTAMP,
    
    INDEX idx_comment (comment_id),
    FOREIGN KEY (comment_id) REFERENCES comments(id) ON DELETE CASCADE
);
```

**4. `pr_assignments` table**
```sql
CREATE TABLE pr_assignments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    purchase_request_id BIGINT UNSIGNED,
    assigned_to_user_id BIGINT UNSIGNED,
    assigned_by_user_id BIGINT UNSIGNED,
    notes TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_pr (purchase_request_id),
    INDEX idx_assigned_to (assigned_to_user_id),
    FOREIGN KEY (purchase_request_id) REFERENCES purchase_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to_user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**5. `po_assignments` table** (same structure as pr_assignments)
```sql
CREATE TABLE po_assignments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    purchase_order_id BIGINT UNSIGNED,
    assigned_to_user_id BIGINT UNSIGNED,
    assigned_by_user_id BIGINT UNSIGNED,
    notes TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_po (purchase_order_id),
    INDEX idx_assigned_to (assigned_to_user_id),
    FOREIGN KEY (purchase_order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to_user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**6. `pr_follows` table**
```sql
CREATE TABLE pr_follows (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    purchase_request_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED,
    created_at TIMESTAMP,
    
    UNIQUE KEY unique_follow (purchase_request_id, user_id),
    INDEX idx_pr (purchase_request_id),
    INDEX idx_user (user_id),
    FOREIGN KEY (purchase_request_id) REFERENCES purchase_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**7. `po_follows` table** (same structure as pr_follows)

**8. `activity_logs` table** (enhanced from existing activity log)
```sql
CREATE TABLE activity_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    subject_type VARCHAR(255), -- 'App\Models\PurchaseRequest' or 'App\Models\PurchaseOrder'
    subject_id BIGINT UNSIGNED,
    event VARCHAR(100), -- 'created', 'updated', 'commented', 'approved', 'file_uploaded', etc.
    description TEXT,
    user_id BIGINT UNSIGNED,
    properties JSON, -- Additional data (old values, new values, etc.)
    created_at TIMESTAMP,
    
    INDEX idx_subject (subject_type, subject_id),
    INDEX idx_user (user_id),
    INDEX idx_event (event),
    INDEX idx_created (created_at)
);
```

### 1.4 UI/UX Design

#### Comment Interface

**Location**: Sidebar or bottom section on PR/PO detail pages

**Layout:**
```
┌─────────────────────────────────────────┐
│ Comments (42)                    [+ New]│
├─────────────────────────────────────────┤
│ [Filter: All | Unresolved | Pinned]     │
│ [Sort: Newest | Oldest | Most Liked]    │
├─────────────────────────────────────────┤
│ ┌─────────────────────────────────────┐ │
│ │ 👤 John Doe • 2 hours ago           │ │
│ │                                     │ │
│ │ @jane.smith Can you clarify the    │ │
│ │ quantity for line item 3?          │ │
│ │                                     │ │
│ │ 📎 attachment.pdf (2.3 MB)         │ │
│ │                                     │ │
│ │ [Reply] [Resolve] [Pin]            │ │
│ │                                     │ │
│ │ └─ Reply from Jane Smith           │ │
│ │    "Yes, it should be 100 units"   │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ ┌─────────────────────────────────────┐ │
│ │ [Rich Text Editor]                  │ │
│ │ [@mention] [Attach] [Send]         │ │
│ └─────────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

**Line Item Comments:**
- Small comment icon (💬) next to each line item
- Badge showing comment count
- Click to expand comment thread inline
- Or open in sidebar

#### Activity Timeline

**Location**: Tab on PR/PO detail page, or separate section

**Layout:**
```
┌─────────────────────────────────────────┐
│ Activity Timeline                       │
├─────────────────────────────────────────┤
│ [Filter: All | Comments | Files | ...]  │
├─────────────────────────────────────────┤
│ Today                                   │
│ ┌─────────────────────────────────────┐ │
│ │ 🕐 14:30                            │ │
│ │ 👤 John Doe commented               │ │
│ │ "Please review the pricing"         │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ ┌─────────────────────────────────────┐ │
│ │ 🕐 13:45                            │ │
│ │ ✅ Status changed: Draft → Submitted│ │
│ └─────────────────────────────────────┘ │
│                                         │
│ Yesterday                               │
│ ┌─────────────────────────────────────┐ │
│ │ 🕐 16:20                            │ │
│ │ 📎 File uploaded: quote.pdf         │ │
│ └─────────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

### 1.5 Technical Implementation

#### Backend (Laravel)

**Models:**
- `Comment` model (polymorphic to PR/PO)
- `CommentMention` model
- `CommentAttachment` model
- `PrAssignment` / `PoAssignment` models
- `PrFollow` / `PoFollow` models
- Enhanced `ActivityLog` model

**Controllers:**
- `CommentController` (CRUD operations)
- `AssignmentController` (assign/unassign)
- `FollowController` (follow/unfollow)
- `ActivityController` (activity timeline)

**Services:**
- `CommentService` (business logic for comments)
- `MentionService` (parse @mentions, send notifications)
- `ActivityService` (log activities)

**Events/Notifications:**
- `CommentCreated` event
- `UserMentioned` event
- `ItemAssigned` event
- `ItemFollowed` event

**API Endpoints:**
```
POST   /api/comments                     - Create comment
GET    /api/comments/{id}                - Get comment
PUT    /api/comments/{id}                - Update comment
DELETE /api/comments/{id}                - Delete comment
GET    /api/pr/{id}/comments             - Get all PR comments
GET    /api/po/{id}/comments             - Get all PO comments
POST   /api/pr/{id}/assign               - Assign PR
POST   /api/po/{id}/assign               - Assign PO
POST   /api/pr/{id}/follow               - Follow PR
POST   /api/po/{id}/follow               - Follow PO
GET    /api/pr/{id}/activity             - Get PR activity timeline
GET    /api/po/{id}/activity             - Get PO activity timeline
```

#### Frontend (JavaScript/Blade)

**Libraries:**
- **Rich Text Editor**: Quill.js or TinyMCE (lightweight, customizable)
- **Autocomplete**: Select2 or custom @mention autocomplete
- **Real-time Updates**: Laravel Echo + Pusher (optional) or polling

**Components:**
- `CommentThread.vue` or `comment-thread.blade.php`
- `CommentEditor.vue` or `comment-editor.blade.php`
- `ActivityTimeline.vue` or `activity-timeline.blade.php`
- `MentionAutocomplete.js`

**Features:**
- AJAX-based comment loading
- Infinite scroll for comments
- Real-time comment updates (optional WebSocket)
- Rich text editor with toolbar
- File upload with progress bar
- @mention autocomplete dropdown

### 1.6 Implementation Steps

**Phase 3.1: Core Comments (Week 1-2)**
1. Create database migrations
2. Create Comment model and relationships
3. Create CommentController with CRUD
4. Build basic comment UI (list + add)
5. Add comment form to PR/PO detail pages
6. Test basic commenting

**Phase 3.2: Threading & Features (Week 3)**
1. Implement threaded replies
2. Add rich text editor
3. Implement @mentions parsing
4. Add file attachments to comments
5. Add edit/delete functionality
6. Test all features

**Phase 3.3: Line Item Comments (Week 4)**
1. Add line item comment support
2. Update UI to show comment indicators
3. Implement inline comment display
4. Test line item commenting

**Phase 3.4: Activity Timeline (Week 5)**
1. Create ActivityLog model/service
2. Log all relevant activities
3. Build activity timeline UI
4. Add filtering and search
5. Test timeline

**Phase 3.5: Collaboration Features (Week 6)**
1. Implement assignment system
2. Implement follow/watchlist
3. Create dashboard widgets
4. Add notifications for assignments/follows
5. Test collaboration features

**Phase 3.6: Polish & Testing (Week 7)**
1. UI/UX improvements
2. Performance optimization
3. Comprehensive testing
4. Documentation
5. User training materials

---

## 2. Document Management Enhancements

### 2.1 Current State Analysis

**What exists now:**
- ✅ Basic file attachments (`po_attachments`, `pr_attachments`)
- ✅ File upload functionality
- ✅ File storage in `storage/app/public/po-attachments/` and `storage/app/public/pr-attachments/`
- ✅ Excel preview functionality (partial)
- ❌ No PDF/image preview
- ❌ No document versioning
- ❌ No document templates
- ❌ No centralized document library
- ❌ No document categories/tags
- ❌ No document search

**Limitations:**
- Files are just stored, no management
- No way to track document versions
- No preview for common file types
- No organization system
- No templates for common documents

### 2.2 Proposed Features

#### A. Document Preview System

**1. Inline Preview**
- **PDF Preview**: 
  - Use PDF.js library for browser-based PDF rendering
  - Zoom in/out, page navigation
  - Full-screen mode
  - Download option
- **Image Preview**:
  - Lightbox gallery for images
  - Zoom, rotate, download
  - Support: JPG, PNG, GIF, WebP
- **Excel Preview** (enhance existing):
  - Better table rendering
  - Sheet navigation
  - Column sorting/filtering
  - Export to PDF
- **Word Document Preview**:
  - Convert DOCX to HTML for preview (using PhpOffice library)
  - Or show "Download to view" message
- **Text File Preview**:
  - Syntax highlighting for code files
  - Line numbers
  - Word wrap

**2. Preview Modal**
- Click attachment → opens modal with preview
- Navigation between multiple files
- File info (name, size, type, upload date, uploader)
- Download button
- Share/link button

#### B. Document Versioning

**Track Document History:**
- When file is replaced, keep old version
- Version numbers (v1, v2, v3, etc.)
- Version comments (why was it updated?)
- Compare versions (for text files)
- Restore previous version
- Download specific version

**Version Features:**
- Automatic versioning on file replacement
- Manual version creation
- Version labeling (e.g., "Final", "Draft", "Revised")
- Version expiration (optional)
- Version access control (who can see old versions)

#### C. Document Templates

**Template Types:**
1. **PO Templates by Supplier**:
   - Pre-filled supplier information
   - Standard terms and conditions
   - Default line items
   - Standard attachments

2. **PR Templates by Department**:
   - Department-specific fields
   - Standard approval workflow
   - Common line items
   - Required attachments

3. **Document Templates**:
   - Standard contract templates
   - Quote request templates
   - Delivery note templates

**Template Features:**
- Create template from existing PR/PO
- Save as template
- Apply template when creating new PR/PO
- Template library (searchable, categorized)
- Template versioning
- Template sharing (department-wide, company-wide)

#### D. Document Library

**Centralized Repository:**
- All documents in one searchable place
- Browse by category, type, date, user
- Advanced search (filename, content, tags, metadata)
- Document collections/folders
- Favorites/bookmarks
- Recent documents

**Document Organization:**
- **Categories**: Contracts, Quotes, Invoices, Reports, etc.
- **Tags**: Custom tags for flexible organization
- **Folders**: Organize by project, department, supplier
- **Metadata**: Custom fields (project code, supplier, date range, etc.)

**Access Control:**
- Document-level permissions
- Category-level permissions
- Department-based access
- Public vs. private documents

### 2.3 Database Design

#### Enhanced Tables

**1. Enhanced `po_attachments` table**
```sql
ALTER TABLE po_attachments ADD COLUMN version INT DEFAULT 1;
ALTER TABLE po_attachments ADD COLUMN parent_id BIGINT UNSIGNED NULL; -- For versioning
ALTER TABLE po_attachments ADD COLUMN version_comment TEXT NULL;
ALTER TABLE po_attachments ADD COLUMN category VARCHAR(100) NULL;
ALTER TABLE po_attachments ADD COLUMN tags JSON NULL;
ALTER TABLE po_attachments ADD COLUMN metadata JSON NULL;
ALTER TABLE po_attachments ADD COLUMN is_template BOOLEAN DEFAULT FALSE;
ALTER TABLE po_attachments ADD COLUMN template_name VARCHAR(255) NULL;
ALTER TABLE po_attachments ADD COLUMN download_count INT DEFAULT 0;
ALTER TABLE po_attachments ADD COLUMN last_accessed_at TIMESTAMP NULL;
ALTER TABLE po_attachments ADD INDEX idx_version (version);
ALTER TABLE po_attachments ADD INDEX idx_category (category);
ALTER TABLE po_attachments ADD INDEX idx_template (is_template);
```

**2. Enhanced `pr_attachments` table** (same enhancements)

**3. New `document_templates` table**
```sql
CREATE TABLE document_templates (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    type ENUM('po', 'pr', 'document') NOT NULL,
    description TEXT NULL,
    supplier_id BIGINT UNSIGNED NULL,
    department_id BIGINT UNSIGNED NULL,
    template_data JSON, -- Store template structure/data
    is_active BOOLEAN DEFAULT TRUE,
    is_public BOOLEAN DEFAULT FALSE,
    created_by_user_id BIGINT UNSIGNED,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_type (type),
    INDEX idx_supplier (supplier_id),
    INDEX idx_department (department_id),
    INDEX idx_active (is_active),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by_user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

**4. New `document_library` table**
```sql
CREATE TABLE document_library (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    file_path VARCHAR(500),
    file_type VARCHAR(100),
    file_size BIGINT UNSIGNED,
    mime_type VARCHAR(100),
    category VARCHAR(100),
    tags JSON NULL,
    metadata JSON NULL,
    description TEXT NULL,
    folder_path VARCHAR(500) NULL,
    is_public BOOLEAN DEFAULT FALSE,
    uploaded_by_user_id BIGINT UNSIGNED,
    download_count INT DEFAULT 0,
    last_accessed_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEX idx_category (category),
    INDEX idx_public (is_public),
    INDEX idx_uploaded_by (uploaded_by_user_id),
    FULLTEXT idx_search (name, description)
);
```

**5. New `document_permissions` table**
```sql
CREATE TABLE document_permissions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    document_type ENUM('attachment', 'library', 'template') NOT NULL,
    document_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED NULL,
    role_id BIGINT UNSIGNED NULL,
    department_id BIGINT UNSIGNED NULL,
    permission ENUM('view', 'download', 'edit', 'delete') NOT NULL,
    created_at TIMESTAMP,
    
    INDEX idx_document (document_type, document_id),
    INDEX idx_user (user_id),
    INDEX idx_role (role_id),
    INDEX idx_department (department_id)
);
```

### 2.4 UI/UX Design

#### Document Preview Modal

```
┌─────────────────────────────────────────┐
│ quote.pdf                    [×] [Full] │
├─────────────────────────────────────────┤
│ [← Prev] [1/5] [Next →]                │
├─────────────────────────────────────────┤
│                                         │
│        [PDF Preview Area]               │
│                                         │
│                                         │
├─────────────────────────────────────────┤
│ File: quote.pdf (2.3 MB)               │
│ Uploaded: 2025-11-03 by John Doe       │
│ [Download] [Share] [Version History]   │
└─────────────────────────────────────────┘
```

#### Document Library Page

```
┌─────────────────────────────────────────┐
│ Document Library                        │
├─────────────────────────────────────────┤
│ [Search...] [Filter: All] [Sort: Date] │
│                                         │
│ Categories: [All] [Contracts] [Quotes] │
│ Tags: [invoice] [urgent] [2025]        │
├─────────────────────────────────────────┤
│ ┌─────────┐ ┌─────────┐ ┌─────────┐   │
│ │ 📄      │ │ 📄      │ │ 📄      │   │
│ │ doc1.pdf│ │ doc2.xls│ │ doc3.jpg│   │
│ │ 2.3 MB  │ │ 1.5 MB  │ │ 500 KB  │   │
│ │ [View]  │ │ [View]  │ │ [View]  │   │
│ └─────────┘ └─────────┘ └─────────┘   │
└─────────────────────────────────────────┘
```

#### Version History

```
┌─────────────────────────────────────────┐
│ Version History: quote.pdf              │
├─────────────────────────────────────────┤
│ v3 (Current) • 2025-11-04 14:30        │
│ "Updated pricing" - John Doe            │
│ [Download] [Restore]                    │
├─────────────────────────────────────────┤
│ v2 • 2025-11-03 10:15                  │
│ "Initial quote" - Jane Smith            │
│ [Download] [View]                       │
├─────────────────────────────────────────┤
│ v1 • 2025-11-02 09:00                  │
│ "First draft" - Jane Smith              │
│ [Download] [View]                       │
└─────────────────────────────────────────┘
```

### 2.5 Technical Implementation

#### Backend (Laravel)

**Libraries:**
- **PDF.js**: Client-side PDF rendering (via CDN)
- **PhpOffice/PhpSpreadsheet**: Excel file manipulation
- **Intervention Image**: Image manipulation and thumbnails
- **League/Flysystem**: Enhanced file storage management

**Services:**
- `DocumentPreviewService`: Generate previews for different file types
- `DocumentVersionService`: Handle versioning logic
- `TemplateService`: Manage templates
- `DocumentLibraryService`: Library operations

**Controllers:**
- `DocumentPreviewController`: Serve previews
- `DocumentVersionController`: Version management
- `TemplateController`: Template CRUD
- `DocumentLibraryController`: Library operations

**API Endpoints:**
```
GET    /api/documents/{id}/preview        - Get document preview
GET    /api/documents/{id}/versions       - Get version history
POST   /api/documents/{id}/versions       - Create new version
GET    /api/documents/{id}/versions/{v}   - Get specific version
POST   /api/documents/{id}/restore/{v}    - Restore version
GET    /api/templates                     - List templates
POST   /api/templates                     - Create template
POST   /api/pr/from-template/{id}         - Create PR from template
POST   /api/po/from-template/{id}         - Create PO from template
GET    /api/library                       - Browse document library
POST   /api/library                       - Upload to library
GET    /api/library/search                - Search library
```

#### Frontend (JavaScript/Blade)

**Libraries:**
- **PDF.js**: For PDF preview (Mozilla's library)
- **Lightbox2** or **GLightbox**: For image galleries
- **FilePond**: Enhanced file upload with preview

**Components:**
- `DocumentPreviewModal.vue` or `document-preview.blade.php`
- `VersionHistory.vue` or `version-history.blade.php`
- `TemplateSelector.vue` or `template-selector.blade.php`
- `DocumentLibrary.vue` or `document-library.blade.php`

### 2.6 Implementation Steps

**Phase 3.7: Document Preview (Week 8-9)**
1. Integrate PDF.js for PDF preview
2. Implement image preview with lightbox
3. Enhance Excel preview
4. Add preview modal component
5. Test all preview types

**Phase 3.8: Document Versioning (Week 10)**
1. Add version columns to attachment tables
2. Create version management service
3. Build version history UI
4. Implement restore functionality
5. Test versioning

**Phase 3.9: Document Templates (Week 11)**
1. Create template tables
2. Build template management UI
3. Implement "Save as Template" functionality
4. Implement "Create from Template" functionality
5. Build template library page
6. Test templates

**Phase 3.10: Document Library (Week 12)**
1. Create document library tables
2. Build library UI (browse, search, filter)
3. Implement document organization (categories, tags)
4. Add document permissions
5. Implement upload to library
6. Test library features

**Phase 3.11: Polish & Integration (Week 13)**
1. Integrate all features
2. Performance optimization
3. UI/UX improvements
4. Comprehensive testing
5. Documentation
6. User training

---

## 3. Integration Points

### 3.1 Comments + Documents
- Comments can have file attachments
- Document preview can have comments
- Activity timeline shows document uploads with comments

### 3.2 Notifications
- Notify users when:
  - Commented on their PR/PO
  - @mentioned in comment
  - Assigned to PR/PO
  - Followed PR/PO has new activity
  - Document version updated
  - Template used

### 3.3 Dashboard Integration
- "My Comments" widget
- "Assigned to Me" widget
- "My Watchlist" widget
- "Recent Documents" widget

---

## 4. Estimated Timeline

**Total Duration**: 13 weeks (3+ months)

- **Comments & Collaboration**: 7 weeks
- **Document Management**: 6 weeks
- **Integration & Polish**: Included in above

**Team Size**: 2-3 developers

---

## 5. Success Metrics

### Comments & Collaboration
- Average comments per PR/PO
- Time to resolution (question → answer)
- User engagement (active commenters)
- Assignment utilization rate

### Document Management
- Document preview usage rate
- Template usage rate
- Version history usage
- Document library search queries

---

## 6. Risk Assessment

### Technical Risks
- **File Storage**: Large files may cause storage issues → Implement file size limits, compression
- **Performance**: Many comments/activities may slow down pages → Implement pagination, lazy loading
- **Browser Compatibility**: PDF.js may not work on all browsers → Provide fallback download option

### User Adoption Risks
- **Complexity**: Users may find features overwhelming → Gradual rollout, user training
- **Change Resistance**: Users may prefer old way → Clear benefits communication, training

---

## 7. Next Steps

1. **Review & Approval**: Get stakeholder approval on this plan
2. **Detailed Design**: Create detailed UI mockups
3. **Prototype**: Build basic prototype for key features
4. **Development**: Start with Phase 3.1 (Core Comments)
5. **Iterative Release**: Release features incrementally for user feedback

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-04  
**Author**: AI Assistant  
**Status**: Ready for Review

