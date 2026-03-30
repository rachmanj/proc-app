# Phase 3 User Guide: Comments & Collaboration Features

**Version**: 1.0  
**Last Updated**: 2025-11-04  
**Audience**: End Users

---

## Table of Contents

1. [Introduction](#introduction)
2. [Comments System](#comments-system)
3. [Line Item Comments](#line-item-comments)
4. [Activity Timeline](#activity-timeline)
5. [Follow Documents](#follow-documents)
6. [Assign Documents](#assign-documents)
7. [My Watchlist](#my-watchlist)
8. [Frequently Asked Questions](#frequently-asked-questions)

---

## Introduction

Phase 3 introduces powerful collaboration features to help your team work together more effectively on Purchase Requests (PRs) and Purchase Orders (POs). These features include:

- **Threaded Comments** - Discuss documents with your team
- **Line Item Comments** - Comment on specific items in a PR/PO
- **@Mentions** - Tag team members to get their attention
- **Activity Timeline** - Track all changes and activities on documents
- **Follow Documents** - Get notified about updates to important documents
- **Assign Documents** - Assign PRs/POs to team members for handling
- **My Watchlist** - View all documents you're following in one place

---

## Comments System

### Overview

The comments system allows you to have threaded conversations about PRs and POs directly on the document page. You can add rich text comments, attach files, mention team members, and reply to comments.

### Accessing Comments

1. Navigate to any PR or PO detail page
2. Scroll down to the **"Comments"** section
3. You'll see all comments related to the document

### Adding a Comment

1. In the **Comments** section, find the comment editor at the bottom
2. Type your comment in the text editor
3. Use the formatting toolbar to:
   - Make text **bold** or *italic*
   - Create bulleted or numbered lists
   - Add links
   - Format as headings
4. (Optional) Click **"Attachments (optional)"** to upload files (max 10MB per file)
5. Click **"Post Comment"** to submit

### Replying to Comments

1. Find the comment you want to reply to
2. Click the **"Reply"** button on that comment
3. Type your reply in the editor that appears
4. Click **"Post Comment"** to submit

**Note**: Replies are nested under the original comment, creating a conversation thread.

### @Mentioning Users

To notify a team member about your comment:

1. Type `@` in the comment editor
2. Start typing the user's name or username
3. Select the user from the dropdown list
4. Continue typing your comment
5. Post the comment

**Result**: The mentioned user will be highlighted in the comment and may receive a notification.

### Comment Actions

Each comment has several action buttons:

- **Edit** (pencil icon) - Edit your own comments
- **Delete** (trash icon) - Delete your own comments
- **Reply** - Reply to the comment
- **Resolve** - Mark a comment thread as resolved (useful for questions/answers)
- **Pin** (pushpin icon) - Pin important comments to the top

### Filtering Comments

Use the filter buttons at the top of the comments section:

- **All** - Show all comments (default)
- **Unresolved** - Show only unresolved comments
- **Pinned** - Show only pinned comments

### File Attachments

- Click **"Attachments (optional)"** before posting a comment
- Select one or more files (PDF, DOCX, XLSX, images, ZIP, etc.)
- Maximum file size: 10MB per file
- Attached files will appear below the comment
- Click on a file to view or download it

---

## Line Item Comments

### Overview

Line item comments allow you to discuss specific items within a PR or PO without cluttering the main document comments.

### Accessing Line Item Comments

1. On a PR or PO detail page, find the **"Purchase Request/Order Details"** table
2. Each line item has a **"Comments"** column with a comment icon (💬)
3. The icon shows a badge with the number of comments (if any)

### Adding a Line Item Comment

1. Click the comment icon (💬) on the line item
2. A modal window will open showing existing comments for that item
3. Scroll to the bottom of the modal
4. Type your comment in the editor
5. (Optional) Attach files
6. Click **"Post Comment"** to submit

### Viewing Line Item Comments

1. Click the comment icon on any line item
2. The modal will show:
   - All comments for that specific line item
   - Reply threads
   - File attachments
   - User mentions

### Comment Count Badge

- The comment icon displays a badge with the number of comments
- The badge updates automatically when new comments are added
- Click the icon to see all comments for that line item

---

## Activity Timeline

### Overview

The Activity Timeline shows a chronological history of all activities on a PR or PO, including comments, status changes, file uploads, approvals, and assignments.

### Accessing Activity Timeline

1. Navigate to a PR or PO detail page
2. Find the **"Activity Timeline"** tab or section
3. The timeline shows all activities from newest to oldest

### Timeline Events

The timeline displays various types of activities:

- **Comments** - When someone adds a comment
- **Status Changes** - When document status changes
- **File Uploads** - When files are attached
- **File Deletions** - When files are removed
- **Approvals** - When documents are approved/rejected
- **Assignments** - When documents are assigned to users
- **Follows** - When users follow documents

### Filtering Activities

Use the filter controls at the top of the timeline:

1. **Event Type** - Filter by activity type:
   - All Events
   - Commented
   - Status Changed
   - File Uploaded
   - Approved/Rejected
   - Assigned
   - Followed

2. **User** - Filter by who performed the activity:
   - All Users
   - Select a specific user

3. **Date Range** - Filter by date:
   - **From** - Start date
   - **To** - End date

4. Click **"Filter"** to apply filters
5. Click **"Clear"** to reset all filters

### Reading the Timeline

Each activity entry shows:
- **Icon** - Visual indicator of activity type
- **User** - Who performed the activity
- **Action** - What was done
- **Timestamp** - When it happened (relative time, e.g., "2 hours ago")
- **Details** - Additional information about the activity

---

## Follow Documents

### Overview

Following a document allows you to track updates and stay informed about changes to important PRs or POs.

### Following a Document

1. On a PR or PO detail page, find the collaboration actions section
2. Click the **"Follow"** button (⭐ icon)
3. The button will change to **"Following"** with a yellow background
4. The follower count badge will update

### Unfollowing a Document

1. Click the **"Following"** button
2. Confirm the action in the popup
3. The button will change back to **"Follow"**

### What Happens When You Follow

- You'll see the document in your **My Watchlist**
- You can track all activities on the document
- The follower count is visible to all users

---

## Assign Documents

### Overview

Assignment allows authorized users (with `assign_document` permission) to assign PRs or POs to buyers for handling.

### Who Can Assign

- Users with **`assign_document`** permission can assign documents
- Typically, this includes:
  - Super Administrators
  - Administrators
  - Procurement Administrators

### Who Can Be Assigned

- Only users with the **Buyer** role can be assigned to documents

### Assigning a Document

1. On a PR or PO detail page, find the collaboration actions section
2. Click the **"Assign"** button (👤 icon)
3. A dropdown menu will open
4. In the **"Select a buyer..."** dropdown:
   - Type to search for a buyer by name or username
   - Select the buyer from the list
5. (Optional) Add notes in the **"Notes"** textarea
6. Click **"Assign"** button
7. The assignment will be saved and the dropdown will close

### Viewing Assignments

- **In the dropdown**: Click "Assign" to see the "Currently Assigned" section
- **On the page**: Assigned users appear as badges below the collaboration buttons
- Each badge shows the user's name
- Click the × icon on a badge to remove the assignment

### Removing an Assignment

1. Click the × icon on an assigned user's badge, OR
2. Click "Assign" → find the user in "Currently Assigned" → click the × icon
3. Confirm the removal in the popup
4. The assignment will be removed

### Assignment Notes

- Notes are optional but recommended
- Notes help explain why the document was assigned
- Notes are visible to all users who can view the document
- Notes appear in the "Currently Assigned" section

---

## My Watchlist

### Overview

Your watchlist shows all PRs and POs that you're following, making it easy to track updates on important documents.

### Accessing Your Watchlist

**Method 1: Navigation Menu**
1. Click **"Procurement"** in the main navigation
2. Click **"My Watchlist"** from the dropdown
3. The badge shows the total count of documents you're following

**Method 2: Direct URL**
- Navigate to: `/procurement/collaboration/watchlist`

### Watchlist Features

The watchlist page displays:

- **Document Type** - Badge showing PR or PO
- **Document Number** - The PR or PO number
- **Item Description** - First item name from the document
- **Status** - Current status of the document
- **Assigned To** - Users currently assigned to the document
- **Last Activity** - Most recent activity with:
  - Who performed the activity
  - What activity was performed
  - When it happened (relative time)
- **Actions** - Buttons to:
  - View the document (👁️ icon)
  - Unfollow the document (⭐ icon)

### Sorting

Documents are automatically sorted by:
- **Most recent activity first** - Documents with recent updates appear at the top
- If no activity, sorted by creation date

### Unfollowing from Watchlist

1. Find the document you want to unfollow
2. Click the **⭐ (star)** button in the Actions column
3. Confirm the action in the popup
4. The document will be removed from your watchlist
5. The page will refresh to show updated counts

### Empty Watchlist

If you're not following any documents, you'll see a message:
> "You are not following any documents yet. Click the **Follow** button on any PR or PO to add it to your watchlist."

---

## Frequently Asked Questions

### Q: Can I edit or delete comments made by other users?

**A**: No. You can only edit or delete your own comments. This ensures accountability and prevents accidental changes to others' messages.

### Q: What file types can I attach to comments?

**A**: Most common file types are supported, including:
- Documents: PDF, DOC, DOCX, XLS, XLSX
- Images: JPG, PNG, GIF
- Archives: ZIP, RAR
- Maximum file size: 10MB per file

### Q: How do I know if someone mentioned me in a comment?

**A**: When you're mentioned in a comment:
- Your name will be highlighted in the comment text
- You may receive a notification (depending on system configuration)
- The mention appears in the Activity Timeline

### Q: What's the difference between "Follow" and "Assign"?

**A**: 
- **Follow**: Anyone can follow a document to track updates. It's like "watching" a document.
- **Assign**: Only authorized users can assign documents to buyers. Assignment indicates responsibility for handling the document.

### Q: Can I assign a document to multiple buyers?

**A**: Yes! You can assign a document to multiple buyers. Each assignment can have its own notes.

### Q: What happens if I unfollow a document?

**A**: When you unfollow:
- The document is removed from your watchlist
- You'll no longer see it in your "My Watchlist" page
- You can still view and comment on the document
- You won't automatically get updates about changes

### Q: Can I filter comments by a specific user?

**A**: Currently, the Activity Timeline allows filtering by user. The Comments section shows all comments, but you can use the Activity Timeline to see activities from specific users.

### Q: How do I resolve a comment thread?

**A**: 
1. Find the comment thread (question/answer discussion)
2. Click the **"Resolve"** button on any comment in the thread
3. The comment will be marked as resolved
4. Use the **"Unresolved"** filter to hide resolved comments

### Q: What does "Pin" do?

**A**: Pinning a comment moves it to the top of the comments list, making it more visible. This is useful for important announcements or key information.

### Q: Can I see who is following a document?

**A**: Yes, the follower count is displayed on the Follow button. However, the list of specific followers is not currently visible to other users.

### Q: How long are activities kept in the Activity Timeline?

**A**: Activities are kept indefinitely in the database. You can use date filters to view activities from specific time periods.

### Q: What if I don't see the "Assign" button?

**A**: The Assign button is only visible to users with the `assign_document` permission. If you don't see it, contact your system administrator to request this permission.

### Q: Can I assign a document to someone who is not a buyer?

**A**: No. Only users with the "Buyer" role can be assigned to documents. This ensures proper workflow and responsibility assignment.

---

## Tips & Best Practices

### Using Comments Effectively

1. **Be Clear and Concise**: Write clear comments that explain your question or concern
2. **Use @Mentions**: Tag relevant team members to get their attention
3. **Resolve Threads**: Mark resolved discussions as resolved to keep the comments section clean
4. **Pin Important Info**: Pin critical information so it's always visible
5. **Use Line Item Comments**: For item-specific questions, use line item comments instead of general comments

### Managing Your Watchlist

1. **Follow Important Documents**: Follow documents you need to track regularly
2. **Regular Review**: Check your watchlist regularly to stay updated
3. **Unfollow When Done**: Unfollow documents that are no longer relevant to avoid clutter
4. **Use Last Activity**: Pay attention to the "Last Activity" column to see which documents have recent updates

### Assignment Best Practices

1. **Add Notes**: Always add notes when assigning to explain the reason
2. **Assign to Appropriate Buyers**: Assign documents to buyers who have the right expertise
3. **Monitor Assignments**: Check assigned users regularly to ensure documents are being handled
4. **Remove Assignments**: Remove assignments when they're no longer needed

### Activity Timeline Tips

1. **Use Filters**: Use filters to find specific activities quickly
2. **Date Range**: Use date ranges to review activities from specific time periods
3. **Check Regularly**: Review the timeline to stay informed about document changes
4. **Export When Needed**: The timeline can be used for audit trails and reporting

---

## Keyboard Shortcuts

Currently, keyboard shortcuts are not implemented. All actions are performed using mouse clicks.

---

## Troubleshooting

### Issue: Comments not loading

**Solution**: 
- Refresh the page
- Check your internet connection
- Clear your browser cache
- Contact support if the issue persists

### Issue: Cannot attach files

**Solution**:
- Check file size (must be under 10MB)
- Verify file type is supported
- Try a different file
- Check browser console for errors

### Issue: Cannot assign document

**Solution**:
- Verify you have `assign_document` permission
- Check that the user you're trying to assign has the "Buyer" role
- Refresh the page
- Contact your administrator if you need the permission

### Issue: Watchlist not showing documents

**Solution**:
- Make sure you're following documents (click Follow button)
- Check that you're logged in with the correct account
- Refresh the page
- Clear browser cache

### Issue: Activity Timeline not showing activities

**Solution**:
- Check date filters - they might be filtering out activities
- Try clearing all filters
- Refresh the page
- Verify the document has activities

---

## Support

For additional support or to report issues:

1. Contact your system administrator
2. Check the system documentation
3. Review the Activity Timeline for recent system changes

---

## Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2025-11-04 | Initial user guide for Phase 3 features |

---

**End of User Guide**

