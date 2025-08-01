@extends('layout.main')

@section('title_page')
    PO Service Details
@endsection

@section('breadcrumb_title')
    po_service
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">PO Service Details</div>
                    <div class="card-tools">
                        <a href="{{ route('po_service.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('po_service.edit', $po_service) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">PO Number</th>
                                    <td>{{ $po_service->po_no }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>{{ \Carbon\Carbon::parse($po_service->date)->format('d-m-Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Vendor</th>
                                    <td>{{ $po_service->supplier->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Project Code</th>
                                    <td>{{ $po_service->project_code }}</td>
                                </tr>
                                <tr>
                                    <th>VAT</th>
                                    <td>{{ $po_service->is_vat ? 'Yes' : 'No' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Created By</th>
                                    <td>{{ $po_service->created_by }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $po_service->created_at->format('d-m-Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated By</th>
                                    <td>{{ $po_service->updated_by ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $po_service->updated_at ? $po_service->updated_at->format('d-m-Y H:i:s') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Remarks:</strong></label>
                                <p class="text-muted">{{ $po_service->remarks ?? 'No remarks' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <form action="{{ route('po_service.destroy', $po_service) }}" method="POST" 
                                onsubmit="return confirm('Are you sure you want to delete this PO Service?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 