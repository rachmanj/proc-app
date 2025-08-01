<!-- Edit Attachment Modal -->
<div class="modal fade" id="editAttachmentModal" tabindex="-1" role="dialog" aria-labelledby="editAttachmentModalLabel" aria-hidden="true">
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
                    <input type="hidden" name="attachment_id" id="edit_attachment_id">
                    
                    <div class="form-group">
                        <label for="edit_keterangan">Keterangan</label>
                        <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3" 
                            placeholder="Enter keterangan for this attachment"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit_file">File</label>
                        <input type="file" class="form-control" id="edit_file" name="file">
                        <small class="form-text text-muted">Leave empty to keep the existing file</small>
                    </div>

                    <div class="form-group">
                        <label>Current File:</label>
                        <div id="current_file_info" class="text-muted"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 