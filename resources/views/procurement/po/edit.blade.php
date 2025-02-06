@extends('layout.main')

@section('title_page')
    Edit Purchase Order
@endsection

@section('breadcrumb_title')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0">
            <li class="breadcrumb-item"><a href="#">Procurement</a></li>
            <li class="breadcrumb-item"><a href="#">Purchase Order</a></li>
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
                        <h5 class="mb-0">Edit Purchase Order</h5>
                        <div>
                            <x-proc-po-links page="list" />
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm ms-2">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <form id="editForm" action="{{ route('procurement.po.update', $purchaseOrder->id) }}" method="POST">
                    @csrf
                    @method('PUT')

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
                        </ul>

                        {{-- Tabs Content --}}
                        <div class="tab-content" id="poTabsContent">
                            {{-- Details Tab --}}
                            <div class="tab-pane fade show active" id="details" role="tabpanel">
                                <div class="p-4">
                                    {{-- Status Badge --}}
                                    <div class="mb-4">
                                        <span
                                            class="badge badge-{{ $purchaseOrder->status === 'draft' ? 'warning' : ($purchaseOrder->status === 'rejected' ? 'danger' : 'info') }} badge-lg">
                                            Status: {{ ucfirst($purchaseOrder->status) }}
                                        </span>
                                    </div>

                                    {{-- Document Information --}}
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0">
                                                <div class="card-body">
                                                    <h6 class="card-subtitle mb-3 text-muted">Document Information</h6>
                                                    <div class="mb-3">
                                                        <label for="doc_num" class="small text-muted">Document
                                                            Number</label>
                                                        <input type="text" name="doc_num" id="doc_num"
                                                            class="form-control @error('doc_num') is-invalid @enderror"
                                                            value="{{ old('doc_num', $purchaseOrder->doc_num) }}" required>
                                                        @error('doc_num')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="doc_date" class="small text-muted">Document Date</label>
                                                        <input type="date" name="doc_date" id="doc_date"
                                                            class="form-control @error('doc_date') is-invalid @enderror"
                                                            value="{{ old('doc_date', $purchaseOrder->doc_date->format('Y-m-d')) }}"
                                                            required>
                                                        @error('doc_date')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div>
                                                        <label for="create_date" class="small text-muted">Create
                                                            Date</label>
                                                        <input type="date" name="create_date" id="create_date"
                                                            class="form-control @error('create_date') is-invalid @enderror"
                                                            value="{{ old('create_date', $purchaseOrder->create_date?->format('Y-m-d')) }}">
                                                        @error('create_date')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light border-0">
                                                <div class="card-body">
                                                    <h6 class="card-subtitle mb-3 text-muted">Supplier Information</h6>
                                                    <div class="mb-3">
                                                        <label for="supplier_id" class="small text-muted">Supplier
                                                            Name</label>
                                                        <select name="supplier_id" id="supplier_id"
                                                            class="form-control select2 @error('supplier_id') is-invalid @enderror"
                                                            required>
                                                            <option value="">Select Supplier</option>
                                                            @foreach ($suppliers as $supplier)
                                                                <option value="{{ $supplier->id }}"
                                                                    {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                                                    {{ $supplier->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('supplier_id')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="project_code" class="small text-muted">Project
                                                            Code</label>
                                                        <input type="text" name="project_code" id="project_code"
                                                            class="form-control @error('project_code') is-invalid @enderror"
                                                            value="{{ old('project_code', $purchaseOrder->project_code) }}">
                                                        @error('project_code')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div>
                                                        <label for="unit_no" class="small text-muted">Unit No</label>
                                                        <input type="text" name="unit_no" id="unit_no"
                                                            class="form-control @error('unit_no') is-invalid @enderror"
                                                            value="{{ old('unit_no', $purchaseOrder->unit_no) }}">
                                                        @error('unit_no')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- PO Items Table --}}
                                    <div class="mt-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Purchase Order Items</h6>
                                            @if ($purchaseOrder->status === 'draft')
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    id="addItemBtn">
                                                    <i class="fas fa-plus"></i> Add Item
                                                </button>
                                            @endif
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="poItemsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="text-center" style="width: 60px">No.</th>
                                                        <th>Item Code</th>
                                                        <th>Description</th>
                                                        <th class="text-end" style="width: 100px">Qty</th>
                                                        <th style="width: 100px">UOM</th>
                                                        <th class="text-end" style="width: 150px">Unit Price</th>
                                                        <th class="text-end" style="width: 150px">Total</th>
                                                        @if ($purchaseOrder->status === 'draft')
                                                            <th style="width: 80px">Action</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($purchaseOrder->purchaseOrderDetails as $index => $detail)
                                                        <tr data-item-id="{{ $detail->id }}">
                                                            <td class="text-center">{{ $index + 1 }}</td>
                                                            <td>
                                                                <input type="hidden"
                                                                    name="items[{{ $index }}][id]"
                                                                    value="{{ $detail->id }}">
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="items[{{ $index }}][item_code]"
                                                                    value="{{ $detail->item_code }}"
                                                                    {{ $purchaseOrder->status !== 'draft' ? 'readonly' : '' }}>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="items[{{ $index }}][description]"
                                                                    value="{{ $detail->description }}"
                                                                    {{ $purchaseOrder->status !== 'draft' ? 'readonly' : '' }}>
                                                            </td>
                                                            <td>
                                                                <input type="number"
                                                                    class="form-control form-control-sm text-end qty-input"
                                                                    name="items[{{ $index }}][qty]"
                                                                    value="{{ $detail->qty }}" step="0.01"
                                                                    {{ $purchaseOrder->status !== 'draft' ? 'readonly' : '' }}>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="items[{{ $index }}][uom]"
                                                                    value="{{ $detail->uom }}"
                                                                    {{ $purchaseOrder->status !== 'draft' ? 'readonly' : '' }}>
                                                            </td>
                                                            <td>
                                                                <input type="number"
                                                                    class="form-control form-control-sm text-end price-input"
                                                                    name="items[{{ $index }}][unit_price]"
                                                                    value="{{ $detail->unit_price }}" step="0.01"
                                                                    {{ $purchaseOrder->status !== 'draft' ? 'readonly' : '' }}>
                                                            </td>
                                                            <td class="text-end">
                                                                <span
                                                                    class="line-total">{{ number_format($detail->qty * $detail->unit_price, 2) }}</span>
                                                            </td>
                                                            @if ($purchaseOrder->status === 'draft')
                                                                <td class="text-center">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger delete-item">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @empty
                                                        <tr id="no-items-row">
                                                            <td colspan="{{ $purchaseOrder->status === 'draft' ? 8 : 7 }}"
                                                                class="text-center text-muted py-3">
                                                                No items found
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="{{ $purchaseOrder->status === 'draft' ? 6 : 5 }}"
                                                            class="text-end">
                                                            <strong>Total Amount:</strong>
                                                        </td>
                                                        <td class="text-end">
                                                            <strong id="grand-total">
                                                                {{ number_format(
                                                                    $purchaseOrder->purchaseOrderDetails->sum(function ($detail) {
                                                                        return $detail->qty * $detail->unit_price;
                                                                    }),
                                                                    2,
                                                                ) }}
                                                            </strong>
                                                        </td>
                                                        @if ($purchaseOrder->status === 'draft')
                                                            <td></td>
                                                        @endif
                                                    </tr>
                                                </tfoot>
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
                                    <div class="row" id="attachments-container">
                                        @foreach ($purchaseOrder->attachments as $attachment)
                                            <div class="col-md-3 col-sm-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body p-2">
                                                        <div class="text-center mb-2">
                                                            @if (in_array(strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
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
                                                        <div class="btn-group btn-group-sm w-100">
                                                            <a href="{{ asset('storage/' . $attachment->file_path) }}"
                                                                class="btn btn-info" target="_blank">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button type="button"
                                                                class="btn btn-danger delete-attachment"
                                                                data-attachment-id="{{ $attachment->id }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Purchase Order
                            </button>
                            @if ($purchaseOrder->status === 'draft')
                                <button type="submit" form="submitForApprovalForm" class="btn btn-success">
                                    <i class="fas fa-paper-plane me-1"></i> Submit for Approval
                                </button>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Submit for Approval Form --}}
                @if ($purchaseOrder->status === 'draft')
                    <form id="submitForApprovalForm" action="{{ route('procurement.po.submit', $purchaseOrder) }}"
                        method="POST" class="d-none">
                        @csrf
                    </form>
                @endif
            </div>
        </div>
    </div>

    @include('procurement.po.edit._upload_attachment_modal')
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
            // Disable form fields if PO is not in draft status
            @if ($purchaseOrder->status !== 'draft')
                $('#editForm input, #editForm select, #editForm textarea').prop('disabled', true);
                $('#editForm button[type="submit"]').prop('disabled', true);
                $('.delete-attachment').prop('disabled', true);
                $('[data-target="#uploadAttachmentsModal"]').prop('disabled', true);
            @endif

            // Handle form submission
            $('#editForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = '';
                        if (xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function(key, value) {
                                errorMessage += value[0] + '\n';
                            });
                        } else {
                            errorMessage = 'Error updating purchase order';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });
                    }
                });
            });

            // Function to refresh attachments
            function refreshAttachments() {
                $.get('{{ route('procurement.po.get-attachments', $purchaseOrder->id) }}', function(response) {
                    const container = $('#attachments-container');
                    container.empty();

                    if (response.attachments && response.attachments.length > 0) {
                        response.attachments.forEach(function(attachment) {
                            const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(
                                attachment.file_path.split('.').pop().toLowerCase()
                            );

                            const preview = isImage ?
                                `<img src="{{ asset('storage') }}/${attachment.file_path}" class="img-fluid" style="max-height: 100px;" alt="Preview">` :
                                `<i class="fas fa-file fa-3x text-secondary"></i>`;

                            container.append(`
                                <div class="col-md-3 col-sm-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body p-2">
                                            <div class="text-center mb-2">
                                                ${preview}
                                            </div>
                                            <p class="small text-muted mb-1 text-truncate" title="${attachment.original_name}">
                                                ${attachment.original_name}
                                            </p>
                                            <div class="btn-group btn-group-sm w-100">
                                                <a href="{{ asset('storage') }}/${attachment.file_path}" class="btn btn-info" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-danger delete-attachment" data-attachment-id="${attachment.id}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        container.append(
                            '<div class="col-12"><p class="text-muted">No attachments found.</p></div>');
                    }
                }).fail(function(xhr) {
                    console.error('Error fetching attachments:', xhr);
                });
            }

            // Handle attachment upload
            $('#uploadAttachmentsForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('procurement.po.upload-attachments', $purchaseOrder->id) }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message
                            });

                            $('#uploadAttachmentsModal').modal('hide');
                            $('#uploadAttachmentsForm')[0].reset();
                            $('.custom-file-label').html(
                                'Choose files'); // Reset the file input label
                            $('#selected-files').empty();
                            refreshAttachments();
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Error uploading attachments';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join(
                                '\n');
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });
                    }
                });
            });

            // Add file input change handler
            $('#attachments').on('change', function() {
                const files = $(this)[0].files;
                const fileList = $('#selected-files');
                fileList.empty();

                for (let i = 0; i < files.length; i++) {
                    fileList.append(`<div class="small">${files[i].name}</div>`);
                }

                if (files.length > 0) {
                    $(this).next('.custom-file-label').html(files.length + ' files selected');
                } else {
                    $(this).next('.custom-file-label').html('Choose files');
                }
            });

            // Handle attachment deletion
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
                            url: `{{ route('procurement.po.detach-file', '') }}/${attachmentId}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    );
                                    refreshAttachments();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Error deleting attachment';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire(
                                    'Error!',
                                    errorMessage,
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // Handle submit for approval
            $('#submitForApprovalForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const url = form.attr('action');
                const formData = form.serialize();

                console.log('Submitting to:', url);
                console.log('Form data:', formData);

                Swal.fire({
                    title: 'Submit for Approval?',
                    text: "This purchase order will be locked for editing once submitted. Continue?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: formData,
                            success: function(data) {
                                console.log('Response:', data);
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: data.message,
                                        allowOutsideClick: false
                                    }).then(() => window.location.reload());
                                } else {
                                    throw new Error(data.message ||
                                        'Error submitting purchase order');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error:', error);
                                console.log('XHR:', xhr.responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: xhr.responseJSON?.message ||
                                        'Error submitting purchase order'
                                });
                            }
                        });
                    }
                });
            });

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        });
    </script>
@endsection
