@extends('layout.main')

@section('title_page')
    Purchase Order
@endsection

@section('breadcrumb_title')
    <small>
        procurement / purchase order / edit
    </small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-proc-po-links page="list" />

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Purchase Order</h3>
                </div>

                <form id="editForm" action="{{ route('procurement.po.update', $purchaseOrder->id) }}" method="POST">
                    @csrf
                    @method('PUT')

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
                    </ul>

                    {{-- Tabs Content --}}
                    <div class="tab-content" id="poTabsContent">
                        {{-- Details Tab --}}
                        <div class="tab-pane fade show active" id="details" role="tabpanel">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="doc_num">Document Number</label>
                                            <input type="text" name="doc_num" id="doc_num"
                                                class="form-control @error('doc_num') is-invalid @enderror"
                                                value="{{ old('doc_num', $purchaseOrder->doc_num) }}" required>
                                            @error('doc_num')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="doc_date">Document Date</label>
                                            <input type="date" name="doc_date" id="doc_date"
                                                class="form-control @error('doc_date') is-invalid @enderror"
                                                value="{{ old('doc_date', $purchaseOrder->doc_date->format('Y-m-d')) }}"
                                                required>
                                            @error('doc_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="supplier_name">Supplier Name</label>
                                            <input type="text" name="supplier_name" id="supplier_name"
                                                class="form-control @error('supplier_name') is-invalid @enderror"
                                                value="{{ old('supplier_name', $purchaseOrder->supplier_name) }}" required>
                                            @error('supplier_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="create_date">Create Date</label>
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
                                                        <button type="button" class="btn btn-danger delete-attachment"
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

                    <div class="card-footer">
                        <button type="submit" class="btn btn-sm btn-primary">Update Purchase Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('procurement.po.edit._upload_attachment_modal')
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
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
        });
    </script>
@endsection
