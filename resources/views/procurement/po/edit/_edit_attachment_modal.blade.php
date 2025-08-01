<!-- Edit Attachment Modal -->
<div class="modal fade" id="editAttachmentModal" tabindex="-1" role="dialog"
    aria-labelledby="editAttachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAttachmentModalLabel">Edit Attachment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editAttachmentForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="editAttachmentId" name="attachment_id">
                    <div class="form-group">
                        <label for="editDescription">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3" 
                            placeholder="Enter description for the attachment"></textarea>
                    </div>
                    <div class="form-group mt-3">
                        <label for="editFile">Replace File (Optional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="editFile" name="file">
                            <label class="custom-file-label" for="editFile">Choose file</label>
                        </div>
                        <small class="form-text text-muted">Leave empty to keep the current file</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div> 