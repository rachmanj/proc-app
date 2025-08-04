@extends('layout.main')

@section('title', 'Consignment Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active">Consignment</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $itemCount }}</h3>
                        <p>Total Items</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <a href="{{ route('consignment.item-prices.index') }}" class="small-box-footer">
                        View Items <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $warehouseCount }}</h3>
                        <p>Warehouses</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <a href="{{ route('consignment.warehouses.index') }}" class="small-box-footer">
                        View Warehouses <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $historyCount }}</h3>
                        <p>Price History Records</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <a href="{{ route('consignment.search') }}" class="small-box-footer">
                        Search History <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>Upload</h3>
                        <p>Import Price Data</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-upload"></i>
                    </div>
                    <a href="{{ route('consignment.imports.upload') }}" class="small-box-footer">
                        Upload Now <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recently Added Items</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th>Description</th>
                                    <th>Supplier</th>
                                    <th>Warehouse</th>
                                    <th>Price (IDR)</th>
                                    <th>Date Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentItems as $item)
                                    <tr>
                                        <td>{{ $item->item_code }}</td>
                                        <td>{{ $item->item_description }}</td>
                                        <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                                        <td>{{ $item->warehouse }}</td>
                                        <td>{{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No items found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
