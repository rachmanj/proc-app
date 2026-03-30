<div class="comments-section mt-4" data-type="{{ $type }}" data-id="{{ $id }}"
    data-user-id="{{ auth()->id() ?? 0 }}">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-comments me-2"></i>
                Comments <span class="badge badge-primary" id="comment-count">0</span>
            </h5>
        </div>
        <div class="card-body">
            <div class="comments-filters mb-3">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary active filter-btn"
                        data-filter="all">All</button>
                    <button type="button" class="btn btn-outline-secondary filter-btn"
                        data-filter="unresolved">Unresolved</button>
                    <button type="button" class="btn btn-outline-secondary filter-btn"
                        data-filter="pinned">Pinned</button>
                </div>
            </div>

            <div class="comments-list" id="comments-list" style="max-height: 600px; overflow-y: auto;">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin"></i> Loading comments...
                </div>
            </div>

            <div class="comment-form mt-4 border-top pt-4">
                <form id="comment-form">
                    <input type="hidden" id="parent-id" name="parent_id" value="">
                    <div class="form-group" style="position: relative;">
                        <label>Add a comment</label>
                        <div id="comment-editor" style="min-height: 150px;"></div>
                        <input type="hidden" id="comment-content" name="content">
                        <div id="mention-dropdown" class="mention-dropdown"></div>
                    </div>
                    <div class="form-group">
                        <label for="comment-attachments">Attachments (optional)</label>
                        <input type="file" class="form-control-file" id="comment-attachments" multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip">
                        <small class="text-muted">Max 10MB per file</small>
                        <div id="attachment-preview" class="mt-2"></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary btn-sm" id="cancel-reply"
                            style="display: none;">
                            <i class="fas fa-times me-1"></i> Cancel Reply
                        </button>
                        <div>
                            <button type="button" class="btn btn-secondary btn-sm" id="cancel-edit"
                                style="display: none;">
                                <i class="fas fa-times me-1"></i> Cancel Edit
                            </button>
                            <button type="submit" class="btn btn-primary" id="submit-comment">
                                <i class="fas fa-paper-plane me-1"></i> <span id="submit-text">Post Comment</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        .ql-container {
            font-family: inherit;
            font-size: 14px;
        }

        .comment-item {
            transition: background-color 0.2s;
        }

        .comment-item:hover {
            background-color: #f8f9fa;
        }

        .comment-item.pinned {
            border-left: 4px solid #ffc107;
        }

        .comment-item.resolved {
            opacity: 0.8;
        }

        .reply-item {
            border-left: 3px solid #dee2e6;
            margin-left: 20px;
            padding-left: 15px;
        }

        .comment-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .mention,
        .mention-link {
            background-color: #e3f2fd;
            padding: 2px 6px;
            border-radius: 3px;
            color: #1976d2 !important;
            font-weight: 500;
            text-decoration: none !important;
            cursor: default;
        }

        .mention-link:hover {
            background-color: #bbdefb;
        }

        .ql-editor .mention-link {
            pointer-events: none;
        }

        .attachment-item {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 5px;
        }

        .edit-mode {
            border: 2px solid #007bff;
            border-radius: 4px;
        }

        #attachment-preview .attachment-item {
            display: inline-block;
            margin: 5px;
            padding: 5px 10px;
            background: #f8f9fa;
            border-radius: 4px;
            font-size: 12px;
        }

        .mention-dropdown {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .mention-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .mention-item:hover,
        .mention-item.active {
            background-color: #f0f0f0;
        }

        .mention-item:last-child {
            border-bottom: none;
        }

        .mention-item strong {
            display: block;
            font-weight: 600;
        }

        .mention-item small {
            color: #666;
            font-size: 0.85em;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
        $(document).ready(function() {
            const type = $('.comments-section').data('type');
            const id = $('.comments-section').data('id');
            const currentUserId = $('.comments-section').data('user-id');
            let quill;
            let usersList = [];
            let editingCommentId = null;
            let currentFilter = 'all';

            // Register mention format
            const Mention = Quill.import('formats/link');
            class MentionFormat extends Mention {
                static create(value) {
                    let node = super.create();
                    node.setAttribute('href', '#');
                    node.setAttribute('class', 'mention-link');
                    node.setAttribute('data-mention', value);
                    node.textContent = '@' + value;
                    return node;
                }
            }
            MentionFormat.blotName = 'mention';
            MentionFormat.tagName = 'a';
            Quill.register(MentionFormat, true);

            // Initialize Quill editor
            quill = new Quill('#comment-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{
                            'header': [1, 2, 3, false]
                        }],
                        ['bold', 'italic', 'underline'],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        ['link'],
                        ['code-block'],
                        ['clean']
                    ]
                },
                placeholder: 'Add a comment... (Use @ to mention someone)'
            });

            // Load users for mentions
            loadUsers();

            // Setup mention handler
            setupMentionHandler();

            loadComments();

            // Filter buttons
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                currentFilter = $(this).data('filter');
                loadComments();
            });

            function loadUsers() {
                $.ajax({
                    url: '{{ route('procurement.comments.users.search') }}?q=',
                    method: 'GET',
                    success: function(users) {
                        usersList = users;
                    }
                });
            }

            let mentionDropdownVisible = false;
            let selectedMentionIndex = -1;
            let currentMentionStart = null;

            function setupMentionHandler() {
                quill.on('text-change', function() {
                    if (!quill.getSelection()) return;

                    const text = quill.getText();
                    const cursorPosition = quill.getSelection(true).index;

                    // Check for @ mention
                    const textBeforeCursor = text.substring(0, cursorPosition);
                    const mentionMatch = textBeforeCursor.match(/@(\w*)$/);

                    if (mentionMatch) {
                        const query = mentionMatch[1].toLowerCase();
                        const mentionStart = cursorPosition - mentionMatch[0].length;

                        const filteredUsers = usersList.filter(user =>
                            user.username.toLowerCase().includes(query) ||
                            (user.name && user.name.toLowerCase().includes(query))
                        );

                        if (filteredUsers.length > 0) {
                            currentMentionStart = mentionStart;
                            showMentionDropdown(filteredUsers, query);
                        } else {
                            hideMentionDropdown();
                        }
                    } else {
                        hideMentionDropdown();
                    }
                });

                // Handle keyboard navigation in mention dropdown
                quill.root.addEventListener('keydown', function(e) {
                    if (!mentionDropdownVisible) return;

                    const dropdown = $('#mention-dropdown');
                    const items = dropdown.find('.mention-item');

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        selectedMentionIndex = Math.min(selectedMentionIndex + 1, items.length - 1);
                        updateMentionSelection();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        selectedMentionIndex = Math.max(selectedMentionIndex - 1, -1);
                        updateMentionSelection();
                    } else if (e.key === 'Enter' && selectedMentionIndex >= 0) {
                        e.preventDefault();
                        items.eq(selectedMentionIndex).click();
                    } else if (e.key === 'Escape') {
                        hideMentionDropdown();
                    }
                });

                // Click outside to close
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#comment-editor, #mention-dropdown').length) {
                        hideMentionDropdown();
                    }
                });
            }

            function showMentionDropdown(users, query) {
                mentionDropdownVisible = true;
                selectedMentionIndex = -1;

                let html = '';
                users.slice(0, 5).forEach((user, index) => {
                    html += `
                    <div class="mention-item" data-username="${user.username}" data-index="${index}">
                        <strong>${user.name || user.username}</strong>
                        <small>@${user.username}</small>
                    </div>
                `;
                });

                $('#mention-dropdown').html(html).show();

                // Position dropdown
                const editorBounds = $('#comment-editor').offset();
                const editorHeight = $('#comment-editor').height();
                $('#mention-dropdown').css({
                    top: (editorBounds.top + editorHeight + 5) + 'px',
                    left: editorBounds.left + 'px',
                    width: '300px'
                });

                // Click handler
                $('.mention-item').on('click', function() {
                    const username = $(this).data('username');
                    insertMention(username);
                });
            }

            function hideMentionDropdown() {
                mentionDropdownVisible = false;
                selectedMentionIndex = -1;
                $('#mention-dropdown').hide();
            }

            function updateMentionSelection() {
                const items = $('#mention-dropdown .mention-item');
                items.removeClass('active');
                if (selectedMentionIndex >= 0) {
                    items.eq(selectedMentionIndex).addClass('active');
                }
            }

            function insertMention(username) {
                if (currentMentionStart === null) return;

                const range = quill.getSelection(true);

                // Delete the @ and partial text
                quill.deleteText(currentMentionStart, range.index - currentMentionStart);

                // Insert the full mention with formatting
                quill.insertText(currentMentionStart, `@${username}`, 'mention', username);
                quill.insertText(currentMentionStart + username.length + 1, ' ');

                // Move cursor after mention
                quill.setSelection(currentMentionStart + username.length + 2);

                hideMentionDropdown();
            }

            function loadComments() {
                $.ajax({
                    url: `{{ route('procurement.comments.index', ['type' => ':type', 'id' => ':id']) }}`
                        .replace(':type', type)
                        .replace(':id', id),
                    method: 'GET',
                    success: function(comments) {
                        let filteredComments = comments;

                        if (currentFilter === 'unresolved') {
                            filteredComments = comments.filter(c => !c.is_resolved);
                        } else if (currentFilter === 'pinned') {
                            filteredComments = comments.filter(c => c.is_pinned);
                        }

                        renderComments(filteredComments);
                        $('#comment-count').text(comments.length);
                    },
                    error: function(xhr) {
                        $('#comments-list').html(
                            '<div class="text-center text-danger py-4">Error loading comments</div>'
                        );
                    }
                });
            }

            function renderComments(comments) {
                if (comments.length === 0) {
                    $('#comments-list').html(
                        '<div class="text-center text-muted py-4">No comments yet. Be the first to comment!</div>'
                    );
                    return;
                }

                let html = '';
                comments.forEach(function(comment) {
                    html += renderComment(comment, 0);
                });
                $('#comments-list').html(html);
            }

            function renderComment(comment, depth) {
                const timeAgo = moment(comment.created_at).fromNow();
                const isOwner = comment.user_id == currentUserId;
                const indentClass = depth > 0 ? 'ms-4' : '';
                let classes = 'comment-item mb-3 p-3 border rounded';
                if (comment.is_pinned) classes += ' pinned bg-light';
                if (comment.is_resolved) classes += ' resolved';

                let html = `
                <div class="${classes} ${indentClass}" data-comment-id="${comment.id}" style="margin-left: ${depth * 30}px;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>${comment.user ? comment.user.name : 'Unknown'}</strong>
                            <small class="text-muted ms-2">${timeAgo}</small>
                            ${comment.is_pinned ? '<span class="badge badge-warning ms-2"><i class="fas fa-thumbtack"></i> Pinned</span>' : ''}
                            ${comment.is_resolved ? '<span class="badge badge-success ms-2">Resolved</span>' : ''}
                        </div>
                        <div class="comment-actions">
                            ${isOwner ? `
                                    <button class="btn btn-sm btn-link text-primary edit-comment" data-id="${comment.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-link text-danger delete-comment" data-id="${comment.id}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                ` : ''}
                            <button class="btn btn-sm btn-link text-info reply-comment" data-id="${comment.id}">
                                <i class="fas fa-reply"></i> Reply
                            </button>
                            ${!comment.is_resolved ? `
                                    <button class="btn btn-sm btn-link text-success resolve-comment" data-id="${comment.id}">
                                        <i class="fas fa-check"></i> Resolve
                                    </button>
                                ` : ''}
                            <button class="btn btn-sm btn-link text-warning pin-comment" data-id="${comment.id}" title="${comment.is_pinned ? 'Unpin' : 'Pin'}">
                                <i class="fas fa-thumbtack ${comment.is_pinned ? '' : 'fa-rotate-180'}"></i>
                            </button>
                        </div>
                    </div>
                    <div class="comment-content" id="comment-content-${comment.id}">
                        ${formatCommentContent(comment.content)}
                    </div>
                    ${comment.attachments && comment.attachments.length > 0 ? `
                            <div class="comment-attachments mt-2">
                                ${comment.attachments.map(function(att) {
                                    return `<a href="/storage/${att.file_path}" target="_blank" class="badge badge-info me-1 attachment-item">
                                    <i class="fas fa-paperclip"></i> ${att.file_name}
                                </a>`;
                                }).join('')}
                            </div>
                        ` : ''}
                    <div class="comment-replies mt-2" id="replies-${comment.id}">
                        ${comment.replies && comment.replies.length > 0 ? comment.replies.map(function(reply) {
                            return renderComment(reply, depth + 1);
                        }).join('') : ''}
                    </div>
                </div>
            `;
                return html;
            }

            function formatCommentContent(content) {
                // Quill HTML already has mentions as <a> tags with data-mention attribute
                // Just ensure they're styled properly
                if (typeof content === 'string') {
                    // Replace plain @mentions in case they weren't formatted
                    content = content.replace(/@(\w+)(?![^<]*<\/a>)/g, '<span class="mention">@$1</span>');
                }
                return content;
            }

            // Handle file selection preview
            $('#comment-attachments').on('change', function() {
                const files = this.files;
                let preview = '';
                for (let i = 0; i < files.length; i++) {
                    preview +=
                        `<span class="attachment-item"><i class="fas fa-file"></i> ${files[i].name}</span>`;
                }
                $('#attachment-preview').html(preview);
            });

            $('#comment-form').on('submit', function(e) {
                e.preventDefault();

                const content = quill.root.innerHTML;
                if (quill.getText().trim().length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Empty Comment',
                        text: 'Please enter a comment'
                    });
                    return;
                }

                const formData = new FormData();
                formData.append('content', content);
                formData.append('_token', '{{ csrf_token() }}');

                if (editingCommentId) {
                    formData.append('_method', 'PUT');
                } else {
                    const parentId = $('#parent-id').val();
                    if (parentId) {
                        formData.append('parent_id', parentId);
                    }
                }

                const files = $('#comment-attachments')[0].files;
                for (let i = 0; i < files.length; i++) {
                    formData.append('attachments[]', files[i]);
                }

                const url = editingCommentId ?
                    `{{ route('procurement.comments.update', ['id' => ':id']) }}`.replace(':id',
                        editingCommentId) :
                    `{{ route('procurement.comments.store', ['type' => ':type', 'id' => ':id']) }}`
                    .replace(':type', type)
                    .replace(':id', id);

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(comment) {
                        quill.setContents([]);
                        $('#comment-attachments').val('');
                        $('#attachment-preview').html('');
                        $('#parent-id').val('');
                        $('#cancel-reply').hide();
                        $('#cancel-edit').hide();
                        editingCommentId = null;
                        $('#submit-text').text('Post Comment');
                        loadComments();

                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: editingCommentId ? 'Comment updated successfully' :
                                'Comment posted successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.error || 'Failed to post comment'
                        });
                    }
                });
            });

            // Reply button
            $(document).on('click', '.reply-comment', function() {
                const commentId = $(this).data('id');
                $('#parent-id').val(commentId);
                $('#cancel-reply').show();
                quill.focus();
                $('html, body').animate({
                    scrollTop: $('#comment-editor').offset().top - 100
                }, 500);
            });

            // Cancel reply
            $('#cancel-reply').on('click', function() {
                $('#parent-id').val('');
                $(this).hide();
            });

            // Edit button
            $(document).on('click', '.edit-comment', function() {
                const commentId = $(this).data('id');
                editingCommentId = commentId;

                $.ajax({
                    url: `{{ route('procurement.comments.show', ['id' => ':id']) }}`.replace(':id',
                        commentId),
                    method: 'GET',
                    success: function(comment) {
                        quill.root.innerHTML = comment.content;
                        $('#cancel-edit').show();
                        $('#submit-text').text('Update Comment');
                        $('html, body').animate({
                            scrollTop: $('#comment-editor').offset().top - 100
                        }, 500);
                    }
                });
            });

            // Cancel edit
            $('#cancel-edit').on('click', function() {
                editingCommentId = null;
                quill.setContents([]);
                $(this).hide();
                $('#submit-text').text('Post Comment');
            });

            // Delete button
            $(document).on('click', '.delete-comment', function() {
                const commentId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This comment will be deleted',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Yes, delete it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ route('procurement.comments.destroy', ['id' => ':id']) }}`
                                .replace(':id', commentId),
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function() {
                                loadComments();
                                Swal.fire('Deleted!', 'Comment has been deleted.',
                                    'success');
                            },
                            error: function() {
                                Swal.fire('Error!', 'Failed to delete comment.',
                                    'error');
                            }
                        });
                    }
                });
            });

            // Resolve button
            $(document).on('click', '.resolve-comment', function() {
                const commentId = $(this).data('id');

                $.ajax({
                    url: `{{ route('procurement.comments.toggle-resolve', ['id' => ':id']) }}`
                        .replace(':id', commentId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        loadComments();
                    }
                });
            });

            // Pin button
            $(document).on('click', '.pin-comment', function() {
                const commentId = $(this).data('id');

                $.ajax({
                    url: `{{ route('procurement.comments.toggle-pin', ['id' => ':id']) }}`.replace(
                        ':id', commentId),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function() {
                        loadComments();
                    }
                });
            });
        });
    </script>
@endpush
