<div class="modal fade" id="lineItemCommentsModal" tabindex="-1" role="dialog" aria-labelledby="lineItemCommentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lineItemCommentsModalLabel">
                    <i class="fas fa-comments me-2"></i>
                    Comments for Line Item: <span id="line-item-info"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="line-item-comments-container" style="max-height: 400px; overflow-y: auto; margin-bottom: 20px;">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-spinner fa-spin"></i> Loading comments...
                    </div>
                </div>
                
                <div class="border-top pt-3">
                    <form id="line-item-comment-form">
                        <input type="hidden" id="line-item-comment-id" name="line_item_id" value="">
                        <div class="form-group">
                            <label>Add a comment</label>
                            <div id="line-item-comment-editor" style="min-height: 120px;"></div>
                        </div>
                        <div class="form-group">
                            <label for="line-item-comment-attachments">Attachments (optional)</label>
                            <input type="file" class="form-control-file" id="line-item-comment-attachments" multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip">
                            <small class="text-muted">Max 10MB per file</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-paper-plane me-1"></i> Post Comment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        let lineItemQuill = null;
        let currentLineItemId = null;
        const type = '{{ $type ?? "pr" }}';
        const id = {{ $id ?? 'null' }};

        // Initialize Quill editor for line item comments
        if ($('#line-item-comment-editor').length) {
            lineItemQuill = new Quill('#line-item-comment-editor', {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link'],
                        ['clean']
                    ]
                },
                placeholder: 'Add a comment about this line item...'
            });
        }

        // Load line item comments when modal opens
        $('#lineItemCommentsModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            currentLineItemId = button.data('line-item-id');
            const itemInfo = button.data('item-info') || 'Line Item';
            
            $('#line-item-info').text(itemInfo);
            $('#line-item-comment-id').val(currentLineItemId);
            if (lineItemQuill) {
                lineItemQuill.setContents([]);
            }
            loadLineItemComments(currentLineItemId);
        });

        function loadLineItemComments(lineItemId) {
            $('#line-item-comments-container').html(
                '<div class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin"></i> Loading comments...</div>'
            );

            $.ajax({
                url: `{{ route('procurement.comments.line-item', ['type' => ':type', 'id' => ':id', 'lineItemId' => ':lineItemId']) }}`
                    .replace(':type', type)
                    .replace(':id', id)
                    .replace(':lineItemId', lineItemId),
                method: 'GET',
                success: function(comments) {
                    if (comments.length === 0) {
                        $('#line-item-comments-container').html(
                            '<div class="text-center text-muted py-4">No comments yet for this line item.</div>'
                        );
                    } else {
                        let html = '<div class="comments-list">';
                        comments.forEach(function(comment) {
                            html += renderComment(comment, 0);
                        });
                        html += '</div>';
                        $('#line-item-comments-container').html(html);
                    }
                },
                error: function() {
                    $('#line-item-comments-container').html(
                        '<div class="text-center text-danger py-4">Error loading comments</div>'
                    );
                }
            });
        }

        function renderComment(comment, depth) {
            const timeAgo = moment(comment.created_at).fromNow();
            const isOwner = comment.user_id == {{ auth()->id() ?? 0 }};
            let classes = 'comment-item mb-3 p-3 border rounded';
            if (comment.is_pinned) classes += ' pinned bg-light';
            if (comment.is_resolved) classes += ' resolved';
            
            let html = `
                <div class="${classes}" data-comment-id="${comment.id}" style="margin-left: ${depth * 30}px;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>${comment.user ? comment.user.name : 'Unknown'}</strong>
                            <small class="text-muted ms-2">${timeAgo}</small>
                            ${comment.is_pinned ? '<span class="badge badge-warning ms-2"><i class="fas fa-thumbtack"></i> Pinned</span>' : ''}
                            ${comment.is_resolved ? '<span class="badge badge-success ms-2">Resolved</span>' : ''}
                        </div>
                    </div>
                    <div class="comment-content">
                        ${formatCommentContent(comment.content)}
                    </div>
                    ${comment.attachments && comment.attachments.length > 0 ? `
                        <div class="comment-attachments mt-2">
                            ${comment.attachments.map(function(att) {
                                return `<a href="/storage/${att.file_path}" target="_blank" class="badge badge-info me-1">
                                    <i class="fas fa-paperclip"></i> ${att.file_name}
                                </a>`;
                            }).join('')}
                        </div>
                    ` : ''}
                </div>
            `;
            return html;
        }

        function formatCommentContent(content) {
            if (typeof content === 'string') {
                content = content.replace(/@(\w+)(?![^<]*<\/a>)/g, '<span class="mention">@$1</span>');
            }
            return content;
        }

        // Handle line item comment form submission
        $('#line-item-comment-form').on('submit', function(e) {
            e.preventDefault();
            
            if (!lineItemQuill) return;
            
            const content = lineItemQuill.root.innerHTML;
            if (lineItemQuill.getText().trim().length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Empty Comment',
                    text: 'Please enter a comment'
                });
                return;
            }

            const formData = new FormData();
            formData.append('content', content);
            formData.append('line_item_id', currentLineItemId);
            formData.append('_token', '{{ csrf_token() }}');

            const files = $('#line-item-comment-attachments')[0].files;
            for (let i = 0; i < files.length; i++) {
                formData.append('attachments[]', files[i]);
            }

            $.ajax({
                url: `{{ route('procurement.comments.store', ['type' => ':type', 'id' => ':id']) }}`
                    .replace(':type', type)
                    .replace(':id', id),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(comment) {
                    lineItemQuill.setContents([]);
                    $('#line-item-comment-attachments').val('');
                    loadLineItemComments(currentLineItemId);
                    
                    // Refresh comment counts if function exists
                    if (typeof window.loadCommentCounts === 'function') {
                        window.loadCommentCounts();
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Comment posted successfully',
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
    });
</script>
@endpush

