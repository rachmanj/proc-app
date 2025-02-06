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
                                                    <th>Item Code</th>
                                                    <th>Description</th>
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
                                                        <td>{{ $detail->item_code }}</td>
                                                        <td>{{ $detail->description }}</td>
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
                            <div class="card-body">
                                <div class="row" id="attachments-container">
                                    @foreach ($purchaseOrder->attachments as $attachment)
                                        <div class="col-md-3 col-sm-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body p-2">
                                                    <div class="text-center mb-2">
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
                                                                class="img-fluid" style="max-height: 100px;"
                                                                alt="Attachment preview">
                                                        @else
                                                            <i class="fas fa-file fa-3x text-secondary"></i>
                                                        @endif
                                                    </div>
                                                    <p class="small text-muted mb-1 text-truncate"
                                                        title="{{ $attachment->original_name }}">
                                                        {{ $attachment->original_name }}
                                                    </p>
                                                    <div class="text-center">
                                                        <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                            class="btn btn-xs btn-info" target="_blank">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if ($purchaseOrder->attachments->isEmpty())
                                        <div class="col-12">
                                            <p class="text-muted">No attachments found.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Approvals Tab --}}
                        <div class="tab-pane fade" id="approvals" role="tabpanel">
                            <div class="card-body">
                                <div class="timeline">
                                    @if ($purchaseOrder->approvals->isEmpty())
                                        <div class="text-center text-muted">
                                            @if ($purchaseOrder->status === 'draft')
                                                <p>Purchase Order has not been submitted for approval yet.</p>
                                            @else
                                                <p>No approval history found.</p>
                                            @endif
                                        </div>
                                    @else
                                        @foreach ($purchaseOrder->approvals->sortBy('approval_level.level') as $approval)
                                            <div class="timeline-item">
                                                <div class="timeline-item-marker">
                                                    <div
                                                        class="timeline-item-marker-indicator bg-{{ $approval->status === 'pending' ? 'warning' : ($approval->status === 'approved' ? 'success' : 'danger') }}">
                                                        <i
                                                            class="fas fa-{{ $approval->status === 'pending' ? 'clock' : ($approval->status === 'approved' ? 'check' : 'times') }}"></i>
                                                    </div>
                                                </div>
                                                <div class="timeline-item-content">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="font-weight-bold">
                                                            Level {{ $approval->approval_level->level }}:
                                                            {{ $approval->approval_level->name }}
                                                        </span>
                                                        <span
                                                            class="badge badge-{{ $approval->status === 'pending' ? 'warning' : ($approval->status === 'approved' ? 'success' : 'danger') }}">
                                                            {{ ucfirst($approval->status) }}
                                                        </span>
                                                    </div>
                                                    @if ($approval->approver)
                                                        <div class="text-muted small">
                                                            Approved by: {{ $approval->approver->user->name }}
                                                        </div>
                                                    @endif
                                                    @if ($approval->notes)
                                                        <div class="mt-2">
                                                            <strong>Notes:</strong>
                                                            <p class="mb-0">{{ $approval->notes }}</p>
                                                        </div>
                                                    @endif
                                                    @if ($approval->approved_at)
                                                        <div class="text-muted small mt-1">
                                                            {{ $approval->approved_at->format('d M Y H:i:s') }}
                                                        </div>
                                                    @endif

                                                    @if (
                                                        $approval->status === 'pending' &&
                                                            auth()->user()->approvers->contains('approval_level_id', $approval->approval_level_id))
                                                        <div class="mt-3">
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
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
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
        });
    </script>
@endsection
