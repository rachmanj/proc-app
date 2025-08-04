@extends('layout.main')

@section('title', 'Add New Item Price')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.dashboard') }}">Consignment</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.item-prices.index') }}">Item Prices</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Add New Item Price</h3>
                    </div>
                    <form action="{{ route('consignment.item-prices.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
                                        <select class="form-control @error('supplier_id') is-invalid @enderror"
                                            id="supplier_id" name="supplier_id" required>
                                            <option value="">-- Select Supplier --</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="item_code">Item Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('item_code') is-invalid @enderror"
                                            id="item_code" name="item_code" value="{{ old('item_code') }}" required>
                                        @error('item_code')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="item_description">Item Description <span
                                                class="text-danger">*</span></label>
                                        <input type="text"
                                            class="form-control @error('item_description') is-invalid @enderror"
                                            id="item_description" name="item_description"
                                            value="{{ old('item_description') }}" required>
                                        @error('item_description')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="part_number">Part Number</label>
                                        <input type="text"
                                            class="form-control @error('part_number') is-invalid @enderror" id="part_number"
                                            name="part_number" value="{{ old('part_number') }}">
                                        @error('part_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="brand">Brand</label>
                                        <input type="text" class="form-control @error('brand') is-invalid @enderror"
                                            id="brand" name="brand" value="{{ old('brand') }}">
                                        @error('brand')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="project">Project <span class="text-danger">*</span></label>
                                        <select class="form-control @error('project') is-invalid @enderror" id="project"
                                            name="project" required>
                                            <option value="">-- Select Project --</option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->code }}"
                                                    {{ old('project') == $project->code ? 'selected' : '' }}>
                                                    {{ $project->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('project')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="warehouse">Warehouse <span class="text-danger">*</span></label>
                                        <select class="form-control @error('warehouse') is-invalid @enderror" id="warehouse"
                                            name="warehouse" required>
                                            <option value="">-- Select Warehouse --</option>
                                            @foreach ($warehouses as $warehouse)
                                                <option value="{{ $warehouse->name }}"
                                                    {{ old('warehouse') == $warehouse->name ? 'selected' : '' }}>
                                                    {{ $warehouse->name }} ({{ $warehouse->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('warehouse')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="uom">Unit of Measure <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('uom') is-invalid @enderror"
                                            id="uom" name="uom" value="{{ old('uom') }}" required>
                                        @error('uom')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="qty">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0"
                                            class="form-control @error('qty') is-invalid @enderror" id="qty"
                                            name="qty" value="{{ old('qty') }}" required>
                                        @error('qty')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="price">Price (IDR) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" min="0"
                                            class="form-control @error('price') is-invalid @enderror" id="price"
                                            name="price" value="{{ old('price') }}" required>
                                        @error('price')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                        <input type="date"
                                            class="form-control @error('start_date') is-invalid @enderror"
                                            id="start_date" name="start_date"
                                            value="{{ old('start_date') ?? date('Y-m-d') }}" required>
                                        @error('start_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expired_date">Expiry Date</label>
                                        <input type="date"
                                            class="form-control @error('expired_date') is-invalid @enderror"
                                            id="expired_date" name="expired_date" value="{{ old('expired_date') }}">
                                        @error('expired_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                    rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <a href="{{ route('consignment.item-prices.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
