<div class="modal fade" id="uploadAttachmentsModal" tabindex="-1" role="dialog"
    aria-labelledby="uploadAttachmentsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadAttachmentsModalLabel">Upload Attachments</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadAttachmentsForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="attachments">Select Files</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="attachments" name="attachments[]"
                                multiple>
                            <label class="custom-file-label" for="attachments">Choose files</label>
                        </div>
                        <small class="form-text text-muted">Maximum file size: 5MB per file</small>
                        <div id="selected-files" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
