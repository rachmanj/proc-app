@extends('layout.main')

@section('title', 'Item Prices')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.dashboard') }}">Consignment</a></li>
    <li class="breadcrumb-item active">Item Prices</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Filter Items</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('consignment.item-prices.index') }}" method="GET">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="item_code">Item Code</label>
                                        <input type="text" class="form-control" id="item_code" name="item_code"
                                            value="{{ request('item_code') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="item_description">Description</label>
                                        <input type="text" class="form-control" id="item_description"
                                            name="item_description" value="{{ request('item_description') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="supplier_id">Supplier</label>
                                        <select class="form-control" id="supplier_id" name="supplier_id">
                                            <option value="">-- All Suppliers --</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="warehouse">Warehouse</label>
                                        <input type="text" class="form-control" id="warehouse" name="warehouse"
                                            value="{{ request('warehouse') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                    <a href="{{ route('consignment.item-prices.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Item Prices</h3>
                        <div class="card-tools">
                            @can('crud_consignment')
                                <a href="{{ route('consignment.item-prices.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add New Item
                                </a>
                            @endcan
                            @can('upload_consignment')
                                <a href="{{ route('consignment.imports.upload') }}" class="btn btn-success btn-sm ml-1">
                                    <i class="fas fa-upload"></i> Import from Excel
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th>Description</th>
                                    <th>Brand</th>
                                    <th>Supplier</th>
                                    <th>Warehouse</th>
                                    <th>UOM</th>
                                    <th>Price (IDR)</th>
                                    <th>Start Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($itemPrices as $item)
                                    <tr>
                                        <td>{{ $item->item_code }}</td>
                                        <td>{{ $item->item_description }}</td>
                                        <td>{{ $item->brand ?? 'N/A' }}</td>
                                        <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                                        <td>{{ $item->warehouse }}</td>
                                        <td>{{ $item->uom }}</td>
                                        <td>{{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->start_date->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="{{ route('consignment.item-prices.show', $item->id) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('search_consignment')
                                                <a href="{{ route('consignment.history', $item->item_code) }}"
                                                    class="btn btn-secondary btn-sm">
                                                    <i class="fas fa-history"></i>
                                                </a>
                                            @endcan
                                            @can('crud_consignment')
                                                <form action="{{ route('consignment.item-prices.destroy', $item->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this item?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No item prices found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $itemPrices->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
