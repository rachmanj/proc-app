@extends('layout.main')

@section('title_page')
    Create PO Service
@endsection

@section('breadcrumb_title')
    po_service
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Create New PO Service</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('po_service.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="po_no">PO Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('po_no') is-invalid @enderror" 
                                        id="po_no" name="po_no" value="{{ old('po_no') }}" required>
                                    @error('po_no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                        id="date" name="date" value="{{ old('date') }}" required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendor_code">Vendor <span class="text-danger">*</span></label>
                                    <select class="form-control select2bs4 @error('vendor_code') is-invalid @enderror" 
                                        id="vendor_code" name="vendor_code" required>
                                        <option value="">Select Vendor</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->code }}" {{ old('vendor_code') == $supplier->code ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('vendor_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="project_code">Project Code <span class="text-danger">*</span></label>
                                    <select class="form-control select2bs4 @error('project_code') is-invalid @enderror" 
                                        id="project_code" name="project_code" required>
                                        <option value="">Select Project Code</option>
                                        <option value="017C" {{ old('project_code') == '017C' ? 'selected' : '' }}>017C</option>
                                        <option value="022C" {{ old('project_code') == '022C' ? 'selected' : '' }}>022C</option>
                                        <option value="021C" {{ old('project_code') == '021C' ? 'selected' : '' }}>021C</option>
                                        <option value="025C" {{ old('project_code') == '025C' ? 'selected' : '' }}>025C</option>
                                        <option value="023C" {{ old('project_code') == '023C' ? 'selected' : '' }}>023C</option>
                                        <option value="APS" {{ old('project_code') == 'APS' ? 'selected' : '' }}>APS</option>
                                    </select>
                                    @error('project_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="is_vat" 
                                            name="is_vat" value="1" {{ old('is_vat', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_vat">Include VAT</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                        id="remarks" name="remarks" rows="3">{{ old('remarks') }}</textarea>
                                    @error('remarks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a href="{{ route('po_service.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 

@push('styles')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endpush

@push('scripts')
    <!-- Select2 -->
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
        });
    </script>
@endpush