@extends('layout.main')

@section('title_page')
    Purchase Order Details
@endsection

@section('breadcrumb_title')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0">
            <li class="breadcrumb-item"><a href="#">Procurement</a></li>
            <li class="breadcrumb-item"><a href="#">Purchase Order</a></li>
            <li class="breadcrumb-item active" aria-current="page">Details</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Purchase Order Information</h5>
                        <div>
                            <x-proc-po-links page="list" />
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm ms-2">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    {{-- Tabs Navigation --}}
                    <ul class="nav nav-tabs nav-tabs-custom" id="poTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active px-4" id="details-tab" data-toggle="tab" href="#details"
                                role="tab">
                                <i class="fas fa-file-alt me-2"></i>Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4" id="attachments-tab" data-toggle="tab" href="#attachments"
                                role="tab">
                                <i class="fas fa-paperclip me-2"></i>Attachments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4" id="approvals-tab" data-toggle="tab" href="#approvals" role="tab">
                                <i class="fas fa-check-circle me-2"></i>Approval History
                            </a>
                        </li>
                    </ul>

                    {{-- Tabs Content --}}
                    <div class="tab-content" id="poTabsContent">
                        {{-- Details Tab --}}
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            <div class="p-4">
                                {{-- Status Badge --}}
                                <div class="mb-4">
                                    <span
                                        class="badge badge-{{ $purchaseOrder->status === 'draft' ? 'warning' : 'success' }} badge-lg">
                                        {{ ucfirst($purchaseOrder->status) }}
                                    </span>
                                </div>

                                {{-- PO Information --}}
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <h6 class="card-subtitle mb-3 text-muted">Document Information</h6>
                                                <div class="mb-3">
                                                    <label class="small text-muted d-block">Document Number</label>
                                                    <strong>{{ $purchaseOrder->doc_num }}</strong>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="small text-muted d-block">Document Date</label>
                                                    <strong>{{ $purchaseOrder->doc_date->format('d M Y') }}</strong>
                                                </div>
                                                <div>
                                                    <label class="small text-muted d-block">Create Date</label>
                                                    <strong>{{ $purchaseOrder->create_date ? $purchaseOrder->create_date->format('d M Y') : '-' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card bg-light border-0">
                                            <div class="card-body">
                                                <h6 class="card-subtitle mb-3 text-muted">Supplier Information</h6>
                                                <div class="mb-3">
                                                    <label class="small text-muted d-block">Supplier Name</label>
                                                    <strong>{{ $purchaseOrder->supplier->name ?? '-' }}</strong>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="small text-muted d-block">Project Code</label>
                                                    <strong>{{ $purchaseOrder->project_code ?? '-' }}</strong>
                                                </div>
                                                <div>
                                                    <label class="small text-muted d-block">Unit No</label>
                                                    <strong>{{ $purchaseOrder->unit_no ?? '-' }}</strong>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- PO Items Table --}}
                                <div class="mt-4">
                                    <h6 class="mb-3">Purchase Order Items</h6>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center" style="width: 60px">No.</th>
                                                    <th>Item Code @if($purchaseOrder->details && $purchaseOrder->details->contains('item_code', 'CONSIGNMENT'))<small class="text-muted">(<span class="text-primary">CONSIGNMENT</span>)</small>@endif</th>
                                                    <th>Description @if($purchaseOrder->details && $purchaseOrder->details->contains('item_code', 'CONSIGNMENT'))<small class="text-muted">(<span class="text-primary">CONSIGNMENT</span>)</small>@endif</th>
                                                    <th class="text-end" style="width: 100px">Qty</th>
                                                    <th style="width: 100px">UOM</th>
                                                    <th class="text-end" style="width: 150px">Unit Price</th>
                                                    <th class="text-end" style="width: 150px">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($purchaseOrder->purchaseOrderDetails as $index => $detail)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>
                                                            @if($detail->item_code === 'CONSIGNMENT')
                                                                {{ $detail->remark1 ?? $detail->item_code }}
                                                            @else
                                                                {{ $detail->item_code }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($detail->item_code === 'CONSIGNMENT')
                                                                {{ $detail->remark2 ?? $detail->description }}
                                                            @else
                                                                {{ $detail->description }}
                                                            @endif
                                                        </td>
                                                        <td class="text-end">{{ number_format($detail->qty, 2) }}</td>
                                                        <td>{{ $detail->uom }}</td>
                                                        <td class="text-end">{{ number_format($detail->unit_price, 2) }}
                                                        </td>
                                                        <td class="text-end">
                                                            {{ number_format($detail->qty * $detail->unit_price, 2) }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted py-3">No items
                                                            found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            @if ($purchaseOrder->purchaseOrderDetails->isNotEmpty())
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="6" class="text-end"><strong>Total Amount:</strong>
                                                        </td>
                                                        <td class="text-end">
                                                            <strong>
                                                                {{ number_format(
                                                                    $purchaseOrder->purchaseOrderDetails->sum(function ($detail) {
                                                                        return $detail->qty * $detail->unit_price;
                                                                    }),
                                                                    2,
                                                                ) }}
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Attachments Tab --}}
                        <div class="tab-pane fade" id="attachments" role="tabpanel">
                            <div class="p-4">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px">No</th>
                                                <th class="text-center" style="width: 100px">Preview</th>
                                                <th style="width: 300px">File Name</th>
                                                <th style="width: 200px">Remarks</th>
                                                <th class="text-center" style="width: 100px">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($purchaseOrder->attachments as $index => $attachment)
                                                <tr>
                                                    <td class="text-center">{{ $index + 1 }}</td>
                                                    <td class="text-center">
                                                        @php
                                                            $extension = strtolower(
                                                                pathinfo($attachment->file_path, PATHINFO_EXTENSION),
                                                            );
                                                            $isImage = in_array($extension, [
                                                                'jpg',
                                                                'jpeg',
                                                                'png',
                                                                'gif',
                                                            ]);
                                                        @endphp

                                                        @if ($isImage)
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
                                                        <span class="text-truncate d-inline-block" style="max-width: 300px;" 
                                                            title="{{ $attachment->original_name }}">
                                                            {{ $attachment->original_name }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($attachment->description)
                                                            <span class="text-muted">{{ $attachment->description }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('procurement.po.view-attachment', ['attachmentId' => $attachment->id]) }}"
                                                            class="btn btn-info btn-sm view-attachment-btn" 
                                                            target="_blank"
                                                            data-file-type="{{ $attachment->file_type }}"
                                                            title="View File">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-3">
                                                        <div class="text-muted">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            No attachments found
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Approvals Tab --}}
                        <div class="tab-pane fade" id="approvals" role="tabpanel">
                            <div class="p-4">
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- The time line -->
                                        <div class="timeline">
                                            @if ($purchaseOrder->approvals->isEmpty())
                                                <div>
                                                    <i class="fas fa-info-circle bg-info"></i>
                                                    <div class="timeline-item">
                                                        <h3 class="timeline-header no-border">
                                                            @if ($purchaseOrder->status === 'draft')
                                                                Purchase Order has not been submitted for approval yet.
                                                            @else
                                                                No approval history found.
                                                            @endif
                                                        </h3>
                                                    </div>
                                                </div>
                                                <div>
                                                    <i class="fas fa-clock bg-gray"></i>
                                                </div>
                                            @else
                                                <!-- Group approvals by date -->
                                                @php
                                                    $approvalsByDate = $purchaseOrder->approvals
                                                        ->sortBy('created_at')
                                                        ->groupBy(function ($approval) {
                                                            return $approval->created_at->format('Y-m-d');
                                                        });
                                                @endphp

                                                @foreach ($approvalsByDate as $date => $approvals)
                                                    <!-- Timeline time label -->
                                                    <div class="time-label">
                                                        <span
                                                            class="bg-primary">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
                                                    </div>

                                                    @foreach ($approvals->sortBy('approval_level.level') as $approval)
                                                        <!-- Timeline item -->
                                                        <div>
                                                            @if ($approval->status === 'pending')
                                                                <i class="fas fa-clock bg-warning"></i>
                                                            @elseif($approval->status === 'approved')
                                                                <i class="fas fa-check bg-success"></i>
                                                            @elseif($approval->status === 'rejected')
                                                                <i class="fas fa-times bg-danger"></i>
                                                            @elseif($approval->status === 'revision')
                                                                <i class="fas fa-sync bg-info"></i>
                                                            @else
                                                                <i class="fas fa-file-alt bg-secondary"></i>
                                                            @endif

                                                            <div class="timeline-item">
                                                                @if ($approval->approved_at)
                                                                    <span class="time">
                                                                        <i class="fas fa-clock"></i>
                                                                        {{ $approval->approved_at->format('H:i') }}
                                                                    </span>
                                                                @endif

                                                                <h3 class="timeline-header">
                                                                    <strong>Level
                                                                        {{ $approval->approval_level->level }}:</strong>
                                                                    {{ $approval->approval_level->name }}
                                                                    <span
                                                                        class="badge badge-{{ $approval->status === 'pending' ? 'warning' : ($approval->status === 'approved' ? 'success' : 'danger') }} ml-2">
                                                                        {{ ucfirst($approval->status) }}
                                                                    </span>
                                                                </h3>

                                                                <div class="timeline-body">
                                                                    @if ($approval->approver)
                                                                        <p class="mb-1">
                                                                            <strong>Approver:</strong>
                                                                            {{ $approval->approver->user->name }}
                                                                        </p>
                                                                    @endif

                                                                    @if ($approval->notes)
                                                                        <p class="mb-0">
                                                                            <strong>Notes:</strong> {{ $approval->notes }}
                                                                        </p>
                                                                    @endif
                                                                </div>

                                                                @if (
                                                                    $approval->status === 'pending' &&
                                                                        auth()->user()->approvers->contains('approval_level_id', $approval->approval_level_id))
                                                                    <div class="timeline-footer">
                                                                        <button class="btn btn-success btn-sm approve-btn"
                                                                            data-approval-id="{{ $approval->id }}">
                                                                            <i class="fas fa-check"></i> Approve
                                                                        </button>
                                                                        <button class="btn btn-danger btn-sm reject-btn"
                                                                            data-approval-id="{{ $approval->id }}">
                                                                            <i class="fas fa-times"></i> Reject
                                                                        </button>
                                                                        <button class="btn btn-info btn-sm revise-btn"
                                                                            data-approval-id="{{ $approval->id }}">
                                                                            <i class="fas fa-sync"></i> Request Revision
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @endforeach

                                                <!-- Timeline end -->
                                                <div>
                                                    <i class="fas fa-clock bg-gray"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        .table> :not(caption)>*>* {
            padding: 0.75rem 1rem;
        }

        .card-subtitle {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Handle Approve button click
            $('.approve-btn').on('click', function() {
                const approvalId = $(this).data('approval-id');
                Swal.fire({
                    title: 'Approve Purchase Order',
                    html: `
                        <form id="approvalForm">
                            <div class="form-group">
                                <label for="notes" class="float-left">Notes (optional)</label>
                                <textarea class="form-control" id="notes" rows="3"></textarea>
                            </div>
                        </form>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Approve',
                    cancelButtonText: 'Cancel',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        const notes = document.getElementById('notes').value;
                        return $.ajax({
                            url: `{{ route('procurement.po.approve', ':id') }}`
                                .replace(':id', approvalId),
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                notes: notes
                            }
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Purchase Order has been approved',
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                }).catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.responseJSON?.message ||
                            'Error approving purchase order',
                        icon: 'error'
                    });
                });
            });

            // Handle Reject button click
            $('.reject-btn').on('click', function() {
                const approvalId = $(this).data('approval-id');
                Swal.fire({
                    title: 'Reject Purchase Order',
                    html: `
                        <form id="rejectionForm">
                            <div class="form-group">
                                <label for="notes" class="float-left">Rejection Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="notes" rows="3" required></textarea>
                            </div>
                        </form>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Reject',
                    cancelButtonText: 'Cancel',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        const notes = document.getElementById('notes').value;
                        if (!notes.trim()) {
                            Swal.showValidationMessage('Please provide a reason for rejection');
                            return false;
                        }
                        return $.ajax({
                            url: `{{ route('procurement.po.reject', ':id') }}`.replace(
                                ':id', approvalId),
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                notes: notes
                            }
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Rejected!',
                            text: 'Purchase Order has been rejected',
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                }).catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.responseJSON?.message ||
                            'Error rejecting purchase order',
                        icon: 'error'
                    });
                });
            });

            // Handle Revise button click
            $('.revise-btn').on('click', function() {
                const approvalId = $(this).data('approval-id');
                Swal.fire({
                    title: 'Request Revision',
                    html: `
                        <form id="revisionForm">
                            <div class="form-group">
                                <label for="notes" class="float-left">Revision Notes <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="notes" rows="3" required></textarea>
                            </div>
                        </form>
                    `,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#17a2b8',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Request Revision',
                    cancelButtonText: 'Cancel',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        const notes = document.getElementById('notes').value;
                        if (!notes.trim()) {
                            Swal.showValidationMessage('Please provide revision notes');
                            return false;
                        }
                        return $.ajax({
                            url: `{{ route('procurement.po.revise', ':id') }}`.replace(
                                ':id', approvalId),
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                notes: notes
                            }
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Revision has been requested',
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                }).catch(error => {
                    Swal.fire({
                        title: 'Error!',
                        text: error.responseJSON?.message || 'Error requesting revision',
                        icon: 'error'
                    });
                });
            });

            // Handle view attachment button click for Excel preview
            $(document).on('click', '.view-attachment-btn', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const fileType = $(this).data('file-type');
                const fileName = $(this).closest('tr').find('td:nth-child(2) span').text();
                const fileSize = $(this).closest('tr').find('td:nth-child(4)').text() || 'Unknown';
                
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
                const localPreviewUrl = `{{ route('procurement.po.preview-excel', ':attachmentId') }}`.replace(':attachmentId', attachmentId);
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
        });
    </script>
@endsection
