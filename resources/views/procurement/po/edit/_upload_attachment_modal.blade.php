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
            <form id="uploadAttachmentsForm" action="{{ route('procurement.po.upload-attachments', $purchaseOrder->id) }}" method="POST" enctype="multipart/form-data">
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
                    <div class="form-group">
                        <label for="descriptions">Description</label>
                        <textarea class="form-control" id="descriptions" name="descriptions[]" rows="3" placeholder="Enter description for the first attachment"></textarea>
                        <small class="form-text text-muted">Description for the first attachment. For multiple files, you can add descriptions individually after upload.</small>
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
