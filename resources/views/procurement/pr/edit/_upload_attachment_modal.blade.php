<!-- Upload Attachments Modal -->
<div class="modal fade" id="uploadAttachmentsModal" tabindex="-1" aria-labelledby="uploadAttachmentsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadAttachmentsModalLabel">Upload Attachments</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadAttachmentsForm" action="{{ route('procurement.pr.upload-attachments', $purchaseRequest->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="attachments" class="form-label">Select Files</label>
                        <input type="file" name="attachments[]" id="attachments" class="form-control" multiple required>
                        <div class="form-text">
                            You can select multiple files. Maximum file size: 10MB per file.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="descriptions" class="form-label">Attachment Description</label>
                        <textarea name="descriptions[]" id="descriptions" class="form-control" rows="3" 
                            placeholder="Enter description for the first attachment"></textarea>
                        <div class="form-text">
                            Description for the first attachment. For multiple files, you can add descriptions individually after upload.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div> 