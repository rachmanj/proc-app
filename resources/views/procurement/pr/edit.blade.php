@extends('layout.main')

@section('title_page')
    Edit Purchase Request
@endsection

@section('breadcrumb_title')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0">
            <li class="breadcrumb-item"><a href="#">Procurement</a></li>
            <li class="breadcrumb-item"><a href="#">Purchase Request</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Purchase Request</h5>
                        <div>
                            <x-proc-pr-links page="list" />
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm ms-2">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <form id="editForm" action="{{ route('procurement.pr.update', $purchaseRequest->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body p-0">
                        {{-- Tabs Navigation --}}
                        <ul class="nav nav-tabs nav-tabs-custom" id="prTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active px-4" id="details-tab" data-toggle="tab" href="#details" role="tab">
                                    <i class="fas fa-file-alt me-2"></i>Details
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link px-4" id="attachments-tab" data-toggle="tab" href="#attachments" role="tab">
                                    <i class="fas fa-paperclip me-2"></i>Attachments
                                </a>
                            </li>
                        </ul>

                        {{-- Tabs Content --}}
                        <div class="tab-content" id="prTabsContent">
                            {{-- Details Tab --}}
                            <div class="tab-pane fade show active" id="details" role="tabpanel">
                                <div class="p-4">
                                    {{-- Status Badge --}}
                                    <div class="mb-4">
                                        @php
                                            $badgeClass = 'badge-warning';
                                            if ($purchaseRequest->pr_status === 'OPEN') {
                                                $badgeClass = 'badge-success';
                                            } elseif ($purchaseRequest->pr_status === 'CLOSED') {
                                                $badgeClass = 'badge-secondary';
                                            } elseif ($purchaseRequest->pr_status === 'progress') {
                                                $badgeClass = 'badge-info';
                                            } elseif ($purchaseRequest->pr_status === 'approved') {
                                                $badgeClass = 'badge-primary';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }} badge-lg">
                                            Status: {{ $purchaseRequest->pr_status }}
                                        </span>
                                    </div>

                                    {{-- Document Information --}}
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0">
                                                <div class="card-body">
                                                    <h6 class="card-subtitle mb-3 text-muted">Document Information</h6>
                                                    <div class="mb-3">
                                                        <label for="pr_no" class="small text-muted">PR Number</label>
                                                        <input type="text" name="pr_no" id="pr_no" 
                                                            class="form-control" 
                                                            value="{{ old('pr_no', $purchaseRequest->pr_no) }}" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="pr_draft_no" class="small text-muted">PR Draft Number</label>
                                                        <input type="text" name="pr_draft_no" id="pr_draft_no" 
                                                            class="form-control" 
                                                            value="{{ old('pr_draft_no', $purchaseRequest->pr_draft_no) }}" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="pr_rev_no" class="small text-muted">PR Revision Number</label>
                                                        <input type="text" name="pr_rev_no" id="pr_rev_no" 
                                                            class="form-control" 
                                                            value="{{ old('pr_rev_no', $purchaseRequest->pr_rev_no) }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0">
                                                <div class="card-body">
                                                    <h6 class="card-subtitle mb-3 text-muted">Additional Information</h6>
                                                    <div class="mb-3">
                                                        <label for="priority" class="small text-muted">Priority</label>
                                                        <input type="text" name="priority" id="priority" 
                                                            class="form-control" 
                                                            value="{{ $purchaseRequest->priority }}" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="pr_status" class="small text-muted">Status</label>
                                                        <input type="text" name="pr_status" id="pr_status" 
                                                            class="form-control" 
                                                            value="{{ $purchaseRequest->pr_status }}" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="pr_type" class="small text-muted">Type</label>
                                                        <input type="text" name="pr_type" id="pr_type" 
                                                            class="form-control" 
                                                            value="{{ old('pr_type', $purchaseRequest->pr_type) }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0">
                                                <div class="card-body">
                                                    <h6 class="card-subtitle mb-3 text-muted">Project Information</h6>
                                                    <div class="mb-3">
                                                        <label for="project_code" class="small text-muted">Project Code</label>
                                                        <input type="text" name="project_code" id="project_code" 
                                                            class="form-control" 
                                                            value="{{ old('project_code', $purchaseRequest->project_code) }}" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="for_unit" class="small text-muted">For Unit</label>
                                                        <input type="text" name="for_unit" id="for_unit" 
                                                            class="form-control" 
                                                            value="{{ old('for_unit', $purchaseRequest->for_unit) }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0">
                                                <div class="card-body">
                                                    <h6 class="card-subtitle mb-3 text-muted">Additional Details</h6>
                                                    <div class="mb-3">
                                                        <label for="remarks" class="small text-muted">Remarks</label>
                                                        <textarea name="remarks" id="remarks" rows="3" 
                                                            class="form-control" readonly>{{ old('remarks', $purchaseRequest->remarks) }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- PR Items Table --}}
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Purchase Request Items</h6>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="prItemsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="text-center" style="width: 60px">No.</th>
                                                        <th>Item Code</th>
                                                        <th>Item Name</th>
                                                        <th class="text-end" style="width: 100px">Qty</th>
                                                        <th style="width: 100px">UOM</th>
                                                        <th class="text-end" style="width: 100px">Open Qty</th>
                                                        <th>Status</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($purchaseRequest->details as $index => $detail)
                                                        <tr>
                                                            <td class="text-center">{{ $index + 1 }}</td>
                                                            <td>{{ $detail->item_code }}</td>
                                                            <td>{{ $detail->item_name }}</td>
                                                            <td class="text-end">{{ number_format($detail->quantity) }}</td>
                                                            <td>{{ $detail->uom }}</td>
                                                            <td class="text-end">{{ number_format($detail->open_qty) }}</td>
                                                            <td>
                                                                <span class="badge badge-{{ $detail->status === 'OPEN' ? 'success' : 'warning' }}">
                                                                    {{ $detail->status }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $detail->line_remarks }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="8" class="text-center text-muted py-3">
                                                                No items found
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Attachments Tab --}}
                            <div class="tab-pane fade" id="attachments" role="tabpanel">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 mb-3">
                                            <button type="button" class="btn btn-sm btn-success float-right"
                                                data-toggle="modal" data-target="#uploadAttachmentsModal">
                                                <i class="fas fa-upload"></i> Upload Attachments
                                            </button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-sm">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width: 5%">No</th>
                                                    <th style="width: 15%">Preview</th>
                                                    <th style="width: 25%">File Name</th>
                                                    <th style="width: 20%">Keterangan</th>
                                                    <th style="width: 15%">File Type</th>
                                                    <th style="width: 10%">Size</th>
                                                    <th style="width: 10%" class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="attachments-container">
                                                @foreach ($purchaseRequest->attachments as $index => $attachment)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td class="text-center">
                                                            @if (in_array(strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                                                <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                                                    class="img-fluid" style="max-height: 50px;"
                                                                    alt="Attachment preview">
                                                            @elseif (in_array(strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION)), ['xlsx', 'xls']))
                                                                <i class="fas fa-file-excel fa-2x text-success"></i>
                                                            @elseif (strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION)) === 'pdf')
                                                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                            @else
                                                                <i class="fas fa-file fa-2x text-secondary"></i>
                                                            @endif
                                                        </td>
                                                        
                                                        <td>
                                                            <span class="text-truncate" style="max-width: 200px; display: inline-block;"
                                                                title="{{ $attachment->original_name }}">
                                                                {{ $attachment->original_name }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="text-truncate" style="max-width: 200px; display: inline-block;"
                                                                title="{{ $attachment->keterangan }}">
                                                                {{ $attachment->keterangan }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $attachment->file_type }}</td>
                                                        <td>{{ number_format($attachment->file_size / 1024, 2) }} KB</td>
                                                        <td class="text-center">
                                                            <div class="d-flex justify-content-center align-items-center" style="min-height: 50px;">
                                                                <a href="{{ route('procurement.pr.view-attachment', $attachment->id) }}" 
                                                                    class="btn btn-info btn-xs view-attachment-btn" 
                                                                    target="_blank"
                                                                    data-file-type="{{ $attachment->file_type }}"
                                                                    style="margin-right: 10px;">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <button type="button"
                                                                    class="btn btn-warning btn-xs edit-attachment"
                                                                    data-attachment-id="{{ $attachment->id }}"
                                                                    data-keterangan="{{ $attachment->keterangan }}"
                                                                    data-file-name="{{ $attachment->original_name }}"
                                                                    data-file-type="{{ $attachment->file_type }}"
                                                                    data-file-size="{{ $attachment->file_size }}"
                                                                    title="Edit" style="margin-right: 10px;">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-xs delete-attachment"
                                                                    data-attachment-id="{{ $attachment->id }}" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white py-3">
                        <!-- <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div> -->
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('procurement.pr.edit._upload_attachment_modal')
    @include('procurement.pr.edit._edit_attachment_modal')
    
    <!-- Excel Preview Modal -->
    <div class="modal fade" id="excelPreviewModal" tabindex="-1" role="dialog" aria-labelledby="excelPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="excelPreviewModalLabel">
                        <i class="fas fa-file-excel text-success me-2"></i>
                        Preview Excel File
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <div>
                            <strong id="excelFileName">Loading...</strong>
                            <br>
                            <small class="text-muted" id="excelFileInfo">File information</small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="downloadExcelBtn">
                                <i class="fas fa-download"></i> Download
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="openInNewTabBtn">
                                <i class="fas fa-external-link-alt"></i> Open in New Tab
                            </button>
                        </div>
                    </div>
                    <div class="position-relative" style="height: 70vh;">
                        <div id="excelPreviewLoading" class="d-flex justify-content-center align-items-center h-100">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2">Loading Excel preview...</p>
                            </div>
                        </div>
                        <iframe id="excelPreviewFrame" 
                                style="width: 100%; height: 100%; border: none; display: none;"
                                frameborder="0">
                        </iframe>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.css') }}">
    <style>
        .nav-tabs-custom {
            border-bottom: 1px solid #dee2e6;
            background-color: #f8f9fa;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            color: #6c757d;
            padding: 1rem 1.5rem;
            font-weight: 500;
            border-bottom: 2px solid transparent;
        }

        .nav-tabs-custom .nav-link:hover {
            color: #495057;
            border-color: transparent;
        }

        .nav-tabs-custom .nav-link.active {
            color: #2196f3;
            background: none;
            border-bottom: 2px solid #2196f3;
        }

        .badge-lg {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }

        .form-control {
            border-radius: 0.25rem;
        }

        .form-control:focus {
            border-color: #2196f3;
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
        }

        .select2-container--bootstrap4 .select2-selection {
            border-radius: 0.25rem;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Base URL for attachment viewing - use Laravel route helper
            const viewAttachmentRouteBase = '{{ route("procurement.pr.view-attachment", ":id") }}';

            // Function to refresh attachments table
            function refreshAttachments() {
                console.log('Refreshing attachments...');
                console.log('URL:', '{{ route("procurement.pr.get-attachments", $purchaseRequest) }}');
                
                $.ajax({
                    url: '{{ route("procurement.pr.get-attachments", $purchaseRequest) }}',
                    type: 'GET',
                    success: function(response) {
                        console.log('Response received:', response);
                        console.log('Response success:', response.success);
                        console.log('Attachments count:', response.attachments ? response.attachments.length : 0);
                        
                        if (response.success) {
                            const container = $('#attachments-container');
                            container.empty();
                            
                            if (response.attachments && response.attachments.length > 0) {
                                console.log('Found attachments:', response.attachments);
                                response.attachments.forEach((attachment, index) => {
                                    const attachmentUrl = viewAttachmentRouteBase.replace(':id', attachment.id);
                                    const row = `
                                        <tr>
                                            <td class="text-center">${index + 1}</td>
                                            <td class="text-center">
                                                ${attachment.file_type && attachment.file_type.startsWith('image/') 
                                                    ? `<img src="${attachmentUrl}" alt="${attachment.original_name}" class="img-thumbnail" style="max-width: 50px;">`
                                                    : attachment.file_type && attachment.file_type.includes('excel') || attachment.original_name.toLowerCase().includes('.xlsx') || attachment.original_name.toLowerCase().includes('.xls')
                                                    ? `<i class="fas fa-file-excel fa-2x text-success"></i>`
                                                    : attachment.file_type && attachment.file_type.includes('pdf') || attachment.original_name.toLowerCase().includes('.pdf')
                                                    ? `<i class="fas fa-file-pdf fa-2x text-danger"></i>`
                                                    : `<i class="fas fa-file-alt fa-2x text-secondary"></i>`}
                                            </td>
                                            <td>
                                                <span class="text-truncate" style="max-width: 200px; display: inline-block;" 
                                                      title="${attachment.original_name}">
                                                    ${attachment.original_name}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-truncate" style="max-width: 200px; display: inline-block;" 
                                                      title="${attachment.keterangan || ''}">
                                                    ${attachment.keterangan || '-'}
                                                </span>
                                            </td>
                                            <td>${attachment.file_type || '-'}</td>
                                            <td>${(attachment.file_size / 1024).toFixed(2)} KB</td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center" style="min-height: 50px;">
                                                    <a href="${attachmentUrl}" 
                                                        class="btn btn-info btn-sm view-attachment-btn" 
                                                        data-file-type="${attachment.file_type || ''}"
                                                        style="margin-right: 10px;">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-warning btn-sm edit-attachment" 
                                                            data-attachment-id="${attachment.id}" 
                                                            data-keterangan="${attachment.keterangan || ''}"
                                                            style="margin-right: 10px;">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm delete-attachment" 
                                                            data-attachment-id="${attachment.id}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    `;
                                    container.append(row);
                                });
                                                    } else {
                            console.log('No attachments found');
                            container.append(`
                                <tr>
                                    <td colspan="7" class="text-center">No attachments found</td>
                                </tr>
                            `);
                        }
                    } else {
                        console.error('Error refreshing attachments:', response.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Error loading attachments',
                            position: 'center'
                        });
                    }
                    },
                    error: function(xhr) {
                        console.error('Error refreshing attachments:', xhr);
                        console.error('Error status:', xhr.status);
                        console.error('Error response:', xhr.responseText);
                        const container = $('#attachments-container');
                        container.empty();
                        container.append(`
                            <tr>
                                <td colspan="7" class="text-center text-danger">
                                    Error loading attachments. Please try again.
                                </td>
                            </tr>
                        `);
                    }
                });
            }

            // Event delegation for all attachment-related actions
            $(document).on('submit', '#editAttachmentForm', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const attachmentId = $('#edit_attachment_id').val();
                formData.append('_token', '{{ csrf_token() }}');
                
                $.ajax({
                    url: '{{ route("procurement.pr.update-attachment", ":id") }}'.replace(':id', attachmentId),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#editAttachmentModal').modal('hide');
                            $('#editAttachmentForm')[0].reset();
                            refreshAttachments();
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message || 'Attachment updated successfully',
                                position: 'center'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message || 'Error updating attachment',
                                position: 'center'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Error updating attachment:', xhr);
                        let errorMessage = 'Error updating attachment. Please try again.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                            position: 'center'
                        });
                    }
                });
            });

            $(document).on('click', '.delete-attachment', function() {
                const attachmentId = $(this).data('attachment-id');
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("procurement.pr.detach-file", "") }}/' + attachmentId,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    refreshAttachments();
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message || 'Attachment deleted successfully',
                                        position: 'center'
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: response.message || 'Error deleting attachment',
                                        position: 'center'
                                    });
                                }
                            },
                            error: function(xhr) {
                                console.error('Error deleting attachment:', xhr);
                                let errorMessage = 'Error deleting attachment. Please try again.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: errorMessage,
                                    position: 'center'
                                });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.edit-attachment', function() {
                const attachmentId = $(this).data('attachment-id');
                const keterangan = $(this).data('keterangan');
                
                $('#edit_attachment_id').val(attachmentId);
                $('#edit_keterangan').val(keterangan);
                $('#editAttachmentModal').modal('show');
            });

            // Handle view attachment button click
            $(document).on('click', '.view-attachment-btn', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const fileType = $(this).data('file-type');
                const fileName = $(this).closest('tr').find('td:nth-child(3) span').text();
                const fileSize = $(this).closest('tr').find('td:nth-child(6)').text();
                
                // Check if it's an Excel file
                const isExcelFile = fileType && fileType.includes('excel') || 
                                   fileName.toLowerCase().includes('.xlsx') || 
                                   fileName.toLowerCase().includes('.xls');
                
                if (isExcelFile) {
                    // For Excel files, show preview modal
                    showExcelPreview(url, fileName, fileSize);
                } else {
                    // For other files, open in new tab
                    window.open(url, '_blank');
                }
            });
            
            // Function to show Excel preview using local server-side conversion
            function showExcelPreview(fileUrl, fileName, fileSize) {
                // Update modal content
                $('#excelFileName').text(fileName);
                $('#excelFileInfo').text(`File size: ${fileSize}`);
                
                // Show loading
                $('#excelPreviewLoading').show();
                $('#excelPreviewFrame').hide();
                
                // Show modal
                $('#excelPreviewModal').modal('show');
                
                // Debug: Log the URL
                console.log('File URL:', fileUrl);
                console.log('File Name:', fileName);
                console.log('File Size:', fileSize);
                
                // Extract attachment ID from URL
                const urlParts = fileUrl.split('/');
                const attachmentId = urlParts[urlParts.length - 2]; // Get the ID before 'view'
                console.log('Attachment ID:', attachmentId);
                
                // Create local Excel preview URL
                const localPreviewUrl = `{{ route('procurement.pr.preview-excel', ':id') }}`.replace(':id', attachmentId);
                console.log('Local Preview URL:', localPreviewUrl);
                
                // Use local server-side Excel preview
                const iframe = $('#excelPreviewFrame');
                iframe.attr('src', localPreviewUrl);
                
                // Handle iframe load with timeout
                let loadTimeout = setTimeout(function() {
                    console.log('Local Excel preview timeout...');
                    $('#excelPreviewLoading').hide();
                    $('#excelPreviewFrame').hide();
                    $('#excelPreviewLoading').html(`
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                            <p>Preview timeout. The file might be too large or there's a processing issue.</p>
                            <p class="small text-muted">Tried local server-side conversion</p>
                            <button type="button" class="btn btn-primary" onclick="downloadExcelFile('${fileUrl}', '${fileName}')">
                                <i class="fas fa-download"></i> Download File
                            </button>
                            <button type="button" class="btn btn-secondary mt-2" onclick="window.open('${fileUrl}', '_blank')">
                                <i class="fas fa-external-link-alt"></i> Open in New Tab
                            </button>
                        </div>
                    `);
                }, 30000); // 30 second timeout for local preview
                
                iframe.on('load', function() {
                    clearTimeout(loadTimeout);
                    $('#excelPreviewLoading').hide();
                    $('#excelPreviewFrame').show();
                });
                
                // Handle iframe error
                iframe.on('error', function() {
                    clearTimeout(loadTimeout);
                    console.log('Local Excel preview error...');
                    $('#excelPreviewLoading').hide();
                    $('#excelPreviewFrame').hide();
                    $('#excelPreviewLoading').html(`
                        <div class="text-center">
                            <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                            <p>Unable to preview Excel file. Please download the file to view it.</p>
                            <p class="small text-muted">Server-side conversion failed</p>
                            <button type="button" class="btn btn-primary" onclick="downloadExcelFile('${fileUrl}', '${fileName}')">
                                <i class="fas fa-download"></i> Download File
                            </button>
                            <button type="button" class="btn btn-secondary mt-2" onclick="window.open('${fileUrl}', '_blank')">
                                <i class="fas fa-external-link-alt"></i> Open in New Tab
                            </button>
                        </div>
                    `);
                });
                
                // Set up download button
                $('#downloadExcelBtn').off('click').on('click', function() {
                    downloadExcelFile(fileUrl, fileName);
                });
                
                // Set up open in new tab button
                $('#openInNewTabBtn').off('click').on('click', function() {
                    window.open(fileUrl, '_blank');
                });
            }
            
            // Function to download Excel file
            function downloadExcelFile(fileUrl, fileName) {
                const link = document.createElement('a');
                link.href = fileUrl;
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            // Initial load of attachments
            refreshAttachments();
        });
    </script>
@endsection
