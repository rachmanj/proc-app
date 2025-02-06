@extends('layout.main')

@section('title_page')
    Purchase Order
@endsection

@section('breadcrumb_title')
    <small>
        procurement / purchase order / show
    </small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-proc-po-links page="list" />

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">View Purchase Order</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card">
                    {{-- Tabs Navigation --}}
                    <ul class="nav nav-tabs" id="poTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab">
                                Purchase Order Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="attachments-tab" data-toggle="tab" href="#attachments" role="tab">
                                Attachments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="approvals-tab" data-toggle="tab" href="#approvals" role="tab">
                                Approval History
                            </a>
                        </li>
                    </ul>

                    {{-- Tabs Content --}}
                    <div class="tab-content" id="poTabsContent">
                        {{-- Details Tab --}}
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Document Number</label>
                                            <p class="form-control-static">{{ $purchaseOrder->doc_num }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Document Date</label>
                                            <p class="form-control-static">{{ $purchaseOrder->doc_date->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Supplier Name</label>
                                            <p class="form-control-static">{{ $purchaseOrder->supplier_name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Create Date</label>
                                            <p class="form-control-static">
                                                {{ $purchaseOrder->create_date ? $purchaseOrder->create_date->format('d M Y') : '-' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <p class="form-control-static">
                                                <span
                                                    class="badge badge-{{ $purchaseOrder->status === 'draft' ? 'warning' : 'success' }}">
                                                    {{ ucfirst($purchaseOrder->status) }}
                                                </span>
                                            </p>
                                        </div>
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
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 20px;
        }

        .timeline-item-marker {
            position: absolute;
            left: 0;
            top: 0;
        }

        .timeline-item-marker-indicator {
            width: 24px;
            height: 24px;
            border-radius: 100%;
            text-align: center;
            line-height: 24px;
            color: #fff;
        }

        .timeline-item-content {
            padding: 15px;
            border-radius: 4px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
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
