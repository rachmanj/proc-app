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
                            <a href="{{ route('procurement.po.index', ['page' => 'list']) }}" class="btn btn-outline-secondary btn-sm ms-2">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>

                <form id="editForm" action="{{ route('procurement.po.update', $purchaseOrder->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="submitted_by" value="{{ auth()->user()->name }}">

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
                                    <div class="mb-4 d-flex align-items-center">
                                        <span
                                            class="badge badge-{{ $purchaseOrder->status === 'draft' ? 'warning' : ($purchaseOrder->status === 'rejected' ? 'danger' : 'info') }} badge-lg me-3">
                                            Status: {{ ucfirst($purchaseOrder->status) }}
                                        </span>
                                        &nbsp; &nbsp;
                                        @if($purchaseOrder->submitted_by)
                                            <span class="text-muted">
                                                <i class="fas fa-user me-1"></i>Submitted by: {{ $purchaseOrder->submitted_by }}
                                            </span>
                                        @endif
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
                                                            value="{{ old('doc_num', $purchaseOrder->doc_num) }}" required disabled>
                                                        @error('doc_num')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="doc_date" class="small text-muted">Document Date</label>
                                                        <input type="date" name="doc_date" id="doc_date"
                                                            class="form-control @error('doc_date') is-invalid @enderror"
                                                            value="{{ old('doc_date', $purchaseOrder->doc_date->format('Y-m-d')) }}"
                                                            required disabled>
                                                        @error('doc_date')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div>
                                                        <label for="create_date" class="small text-muted">Create
                                                            Date</label>
                                                        <input type="date" name="create_date" id="create_date"
                                                            class="form-control @error('create_date') is-invalid @enderror"
                                                            value="{{ old('create_date', $purchaseOrder->create_date?->format('Y-m-d')) }}" disabled>
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
                                                            required disabled>
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
                                                            value="{{ old('project_code', $purchaseOrder->project_code) }}" disabled>
                                                        @error('project_code')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div>
                                                        <label for="unit_no" class="small text-muted">Unit No</label>
                                                        <input type="text" name="unit_no" id="unit_no"
                                                            class="form-control @error('unit_no') is-invalid @enderror"
                                                            value="{{ old('unit_no', $purchaseOrder->unit_no) }}" disabled>
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
                                        <!-- <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Purchase Order Items</h6>
                                            @if ($purchaseOrder->status === 'draft' || $purchaseOrder->status === 'revision')
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    id="addItemBtn">
                                                    <i class="fas fa-plus"></i> Add Item
                                                </button>
                                            @endif
                                        </div> -->
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="poItemsTable">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="text-center" style="width: 60px">No.</th>
                                                        <th>Item Code @if($purchaseOrder->details && $purchaseOrder->details->contains('item_code', 'CONSIGNMENT'))<small class="text-muted">(<span class="text-primary">CONSIGNMENT</span>)</small>@endif</th>
                                                        <th>Description @if($purchaseOrder->details && $purchaseOrder->details->contains('item_code', 'CONSIGNMENT'))<small class="text-muted">(<span class="text-primary">CONSIGNMENT</span>)</small>@endif</th>
                                                        <th class="text-end" style="width: 100px">Qty</th>
                                                        <th style="width: 100px">UOM</th>
                                                        <th class="text-end" style="width: 150px">Unit Price</th>
                                                        <th class="text-end" style="width: 150px">Total</th>
                                                        @if ($purchaseOrder->status === 'draft' || $purchaseOrder->status === 'revision')
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
                                                                    value="@if($detail->item_code === 'CONSIGNMENT'){{ $detail->remark1 ?? $detail->item_code }}@else{{ $detail->item_code }}@endif" disabled>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="items[{{ $index }}][description]"
                                                                    value="@if($detail->item_code === 'CONSIGNMENT'){{ $detail->remark2 ?? $detail->description }}@else{{ $detail->description }}@endif" disabled>
                                                            </td>
                                                            <td>
                                                                <input type="number"
                                                                    class="form-control form-control-sm text-end qty-input"
                                                                    name="items[{{ $index }}][qty]"
                                                                    value="{{ $detail->qty }}" step="0.01" disabled>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    name="items[{{ $index }}][uom]"
                                                                    value="{{ $detail->uom }}" disabled>
                                                            </td>
                                                            <td>
                                                                <input type="number"
                                                                    class="form-control form-control-sm text-end price-input"
                                                                    name="items[{{ $index }}][unit_price]"
                                                                    value="{{ $detail->unit_price }}" step="0.01" disabled>
                                                            </td>
                                                            <td class="text-end">
                                                                <span
                                                                    class="line-total">{{ number_format($detail->qty * $detail->unit_price, 2) }}</span>
                                                            </td>
                                                            @if ($purchaseOrder->status === 'draft' || $purchaseOrder->status === 'revision')
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
                                                            <td colspan="{{ $purchaseOrder->status === 'draft' || $purchaseOrder->status === 'revision' ? 8 : 7 }}"
                                                                class="text-center text-muted py-3">
                                                                No items found
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="{{ $purchaseOrder->status === 'draft' || $purchaseOrder->status === 'revision' ? 6 : 5 }}"
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
                                                        @if ($purchaseOrder->status === 'draft' || $purchaseOrder->status === 'revision')
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
                                <div class="p-4">
                                    {{-- PR Attachments Section --}}
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">PR Attachments</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 50px">No</th>
                                                            <th class="text-center" style="width: 100px">Preview</th>
                                                            <th style="width: 300px">File Name</th>
                                                            <th style="width: 200px">Remarks</th>
                                                            <th class="text-center" style="width: 180px">Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($prAttachments as $index => $attachment)
                                                            <tr>
                                                                <td class="text-center">{{ $index + 1 }}</td>
                                                                <td class="text-center">
                                                                    @if (in_array(strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                                                        <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                                                            class="img-fluid" style="max-height: 50px;"
                                                                            alt="Attachment preview">
                                                                    @elseif (in_array(strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION)), ['xlsx', 'xls']))
                                                                        <i class="fas fa-file-excel fa-2x" style="color: #217346;"></i>
                                                                    @elseif (strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION)) === 'pdf')
                                                                        <i class="fas fa-file-pdf fa-2x" style="color: #DC143C;"></i>
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
                                                                    @if($attachment->keterangan)
                                                                        <span class="text-muted">{{ $attachment->keterangan }}</span>
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="d-flex justify-content-center gap-3">
                                                                        <a href="{{ route('procurement.pr.view-attachment', $attachment->id) }}" 
                                                                            class="btn btn-info btn-sm view-attachment-btn" 
                                                                            target="_blank"
                                                                            data-file-type="{{ $attachment->file_type }}"
                                                                            title="View File">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center py-3">
                                                                    <div class="text-muted">
                                                                        <i class="fas fa-info-circle me-1"></i>
                                                                        No PR attachments found
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- PO Attachments Section --}}
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Purchase Order Attachments</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    @if ($purchaseOrder->status === 'draft' || $purchaseOrder->status === 'revision')
                                                        <button type="button" class="btn btn-sm btn-success float-right"
                                                            data-toggle="modal" data-target="#uploadAttachmentsModal">
                                                            <i class="fas fa-upload"></i> Upload Attachments
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="row" id="attachments-container">
                                                <div class="col-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center" style="width: 50px">No</th>
                                                                    <th class="text-center" style="width: 100px">Preview</th>
                                                                    <th style="width: 300px">File Name</th>
                                                                    <th style="width: 200px">Remarks</th>
                                                                    <th class="text-center" style="width: 180px">Actions</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse ($purchaseOrder->attachments as $index => $attachment)
                                                                    <tr>
                                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                                        <td class="text-center">
                                                                            @if (in_array(strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']))
                                                                                <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                                                                    class="img-fluid" style="max-height: 50px;"
                                                                                    alt="Attachment preview">
                                                                                                                                        @elseif (in_array(strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION)), ['xlsx', 'xls']))
                                                                <i class="fas fa-file-excel fa-2x" style="color: #217346;"></i>
                                                            @elseif (strtolower(pathinfo($attachment->file_path, PATHINFO_EXTENSION)) === 'pdf')
                                                                <i class="fas fa-file-pdf fa-2x" style="color: #DC143C;"></i>
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
                                                                            <div class="d-flex justify-content-center gap-3">
                                                                                <a href="{{ route('procurement.po.view-attachment', ['attachmentId' => $attachment->id]) }}" 
                                                                                    class="btn btn-info btn-sm view-attachment-btn" 
                                                                                    target="_blank"
                                                                                    data-file-type="{{ $attachment->file_type }}"
                                                                                    title="View File">
                                                                                    <i class="fas fa-eye"></i>
                                                                                </a>
                                                                                @if ($purchaseOrder->status !== 'submitted')
                                                                                    &nbsp; &nbsp;
                                                                                    <button type="button" 
                                                                                        class="btn btn-warning btn-sm edit-attachment" 
                                                                                        data-attachment-id="${attachment.id}" 
                                                                                        data-description="${attachment.description || ''}"
                                                                                        title="Edit Attachment">
                                                                                        <i class="fas fa-edit"></i>
                                                                                    </button>
                                                                                    &nbsp; &nbsp;
                                                                                    <button type="button" 
                                                                                        class="btn btn-danger btn-sm delete-attachment" 
                                                                                        data-attachment-id="${attachment.id}"
                                                                                        title="Delete Attachment">
                                                                                        <i class="fas fa-trash"></i>
                                                                                    </button>
                                                                                @endif
                                                                            </div>
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary" disabled>
                                <i class="fas fa-save me-1"></i> Update Purchase Order
                            </button>
                            @if ($purchaseOrder->status === 'draft' || $purchaseOrder->status === 'revision')
                                <button type="submit" form="submitForApprovalForm" class="btn btn-success">
                                    <i class="fas fa-paper-plane me-1"></i> Submit for Approval
                                </button>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- Submit for Approval Form --}}
                @if ($purchaseOrder->status === 'draft' || $purchaseOrder->status === 'revision')
                    <form id="submitForApprovalForm" action="{{ route('procurement.po.submit', $purchaseOrder) }}"
                        method="POST" class="d-none">
                        @csrf
                        <input type="hidden" name="submitted_by" value="{{ auth()->user()->name }}">
                    </form>
                @endif
            </div>
        </div>
    </div>

    @include('procurement.po.edit._upload_attachment_modal')
    @include('procurement.po.edit._edit_attachment_modal')
    
    <!-- Excel Preview Modal -->
    <div class="modal fade" id="excelPreviewModal" tabindex="-1" role="dialog" aria-labelledby="excelPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="excelPreviewModalLabel">
                        <i class="fas fa-file-excel me-2" style="color: #217346;"></i>
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

        /* Remark columns styling */
        .remark-textarea {
            word-wrap: break-word;
            white-space: pre-wrap;
        }

        /* Table responsive improvements for remark columns */
        .table-responsive {
            overflow-x: auto;
        }

        /* Ensure remark columns don't shrink too much */
        th[style*="width: 200px"], 
        td:has(.remark-textarea) {
            min-width: 200px;
            max-width: 250px;
        }
    </style>
@endsection

@section('scripts')
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Disable form fields if PO is not in draft or revision status
            @if ($purchaseOrder->status !== 'draft' && $purchaseOrder->status !== 'revision')
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

            // Function to refresh attachments table
            function refreshAttachments() {
                $.ajax({
                    url: '{{ route("procurement.po.get-attachments", $purchaseOrder->id) }}',
                    type: 'GET',
                    success: function(response) {
                        const container = $('#attachments-container');
                        container.empty();
                        
                        let tableHtml = `
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width: 50px">No</th>
                                                <th class="text-center" style="width: 100px">Preview</th>
                                                <th style="width: 300px">File Name</th>
                                                <th style="width: 200px">Remarks</th>
                                                <th class="text-center" style="width: 180px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                        `;
                        
                        if (response.attachments && response.attachments.length > 0) {
                            response.attachments.forEach((attachment, index) => {
                                const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(
                                    attachment.file_path.split('.').pop().toLowerCase()
                                );

                                const fileExtension = attachment.file_path.split('.').pop().toLowerCase();
                                const isExcel = ['xlsx', 'xls'].includes(fileExtension);
                                const isPdf = fileExtension === 'pdf';
                                
                                let preview;
                                if (isImage) {
                                    preview = `<img src="{{ url('procurement/po/attachments') }}/${attachment.id}/view" class="img-fluid" style="max-height: 50px;" alt="Preview">`;
                                } else if (isExcel) {
                                    preview = `<i class="fas fa-file-excel fa-2x" style="color: #217346;"></i>`;
                                } else if (isPdf) {
                                    preview = `<i class="fas fa-file-pdf fa-2x" style="color: #DC143C;"></i>`;
                                } else {
                                    preview = `<i class="fas fa-file fa-2x text-secondary"></i>`;
                                }

                                const description = attachment.description ? 
                                    `<span class="text-muted">${attachment.description}</span>` : 
                                    `<span class="text-muted">-</span>`;

                                const isSubmitted = '{{ $purchaseOrder->status }}' === 'submitted';
                                const viewAttachmentUrl = '{{ url('procurement/po/attachments') }}/' + attachment.id + '/view';
                                const actionButtons = isSubmitted ? 
                                    `<a href="${viewAttachmentUrl}" 
                                        class="btn btn-info btn-sm view-attachment-btn" 
                                        target="_blank"
                                        data-file-type="${attachment.file_type || ''}"
                                        title="View File">
                                        <i class="fas fa-eye"></i>
                                    </a>` :
                                    `<a href="${viewAttachmentUrl}" 
                                        class="btn btn-info btn-sm view-attachment-btn" 
                                        target="_blank"
                                        data-file-type="${attachment.file_type || ''}"
                                        title="View File">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    &nbsp; &nbsp;
                                    <button type="button" 
                                        class="btn btn-warning btn-sm edit-attachment" 
                                        data-attachment-id="${attachment.id}" 
                                        data-description="${attachment.description || ''}"
                                        title="Edit Attachment">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    &nbsp; &nbsp;
                                    <button type="button" 
                                        class="btn btn-danger btn-sm delete-attachment" 
                                        data-attachment-id="${attachment.id}"
                                        title="Delete Attachment">
                                        <i class="fas fa-trash"></i>
                                    </button>`;

                                tableHtml += `
                                    <tr>
                                        <td class="text-center">${index + 1}</td>
                                        <td class="text-center">
                                            ${preview}
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" style="max-width: 300px;" 
                                                title="${attachment.original_name}">
                                                ${attachment.original_name}
                                            </span>
                                        </td>
                                        <td>
                                            ${description}
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-3">
                                                ${actionButtons}
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            tableHtml += `
                                <tr>
                                    <td colspan="5" class="text-center py-3">
                                        <div class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            No attachments found
                                        </div>
                                    </td>
                                </tr>
                            `;
                        }

                        tableHtml += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;

                        container.html(tableHtml);
                    },
                    error: function(xhr) {
                        console.error('Error refreshing attachments:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error loading attachments',
                            position: 'center'
                        });
                    }
                });
            }

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

            // Handle edit attachment form submission
            $(document).on('submit', '#editAttachmentForm', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const attachmentId = $('#editAttachmentId').val();
                
                $.ajax({
                    url: `{{ route('procurement.po.update-attachment', '') }}/${attachmentId}`,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#editAttachmentModal').modal('hide');
                            $('#editAttachmentForm')[0].reset();
                            $('.custom-file-label').html('Choose file');
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
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error updating attachment',
                            position: 'center'
                        });
                    }
                });
            });

            // Handle edit button click
            $(document).on('click', '.edit-attachment', function() {
                const attachmentId = $(this).data('attachment-id');
                const description = $(this).data('description');
                
                $('#editAttachmentId').val(attachmentId);
                $('#editDescription').val(description);
                $('#editAttachmentModal').modal('show');
            });

            // Initial load of attachments
            refreshAttachments();

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

            // Handle view attachment button click
            $(document).on('click', '.view-attachment-btn', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                const fileType = $(this).data('file-type');
                const fileName = $(this).closest('tr').find('td:nth-child(3) span').text();
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
                
                // Determine if it's PR or PO attachment based on URL
                const isPrAttachment = fileUrl.includes('/pr/attachments/');
                const isPoAttachment = fileUrl.includes('/po/attachments/');
                
                // Create appropriate local Excel preview URL
                let localPreviewUrl;
                if (isPrAttachment) {
                    localPreviewUrl = `{{ route('procurement.pr.preview-excel', ':id') }}`.replace(':id', attachmentId);
                } else if (isPoAttachment) {
                    localPreviewUrl = `{{ route('procurement.po.preview-excel', ':attachmentId') }}`.replace(':attachmentId', attachmentId);
                } else {
                    console.error('Unknown attachment type:', fileUrl);
                    return;
                }
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

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        });
    </script>
@endsection
