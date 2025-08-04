@extends('layout.main')

@section('title', 'Item Price Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.dashboard') }}">Consignment</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.item-prices.index') }}">Item Prices</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Item Price Details</h3>
                        <div class="card-tools">
                            @can('search_consignment')
                                <a href="{{ route('consignment.history', $itemPrice->item_code) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-history"></i> View Price History
                                </a>
                            @endcan
                            @can('crud_consignment')
                                <form action="{{ route('consignment.item-prices.destroy', $itemPrice->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this item?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 30%">Item Code</th>
                                        <td>{{ $itemPrice->item_code }}</td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{{ $itemPrice->item_description }}</td>
                                    </tr>
                                    <tr>
                                        <th>Part Number</th>
                                        <td>{{ $itemPrice->part_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Brand</th>
                                        <td>{{ $itemPrice->brand ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Supplier</th>
                                        <td>{{ $itemPrice->supplier->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Project</th>
                                        <td>{{ $itemPrice->project }}</td>
                                    </tr>
                                    <tr>
                                        <th>Warehouse</th>
                                        <td>{{ $itemPrice->warehouse }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 30%">Unit of Measure</th>
                                        <td>{{ $itemPrice->uom }}</td>
                                    </tr>
                                    <tr>
                                        <th>Quantity</th>
                                        <td>{{ number_format($itemPrice->qty, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Price (IDR)</th>
                                        <td>{{ number_format($itemPrice->price, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Start Date</th>
                                        <td>{{ $itemPrice->start_date->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Expiry Date</th>
                                        <td>{{ $itemPrice->expired_date ? $itemPrice->expired_date->format('d-m-Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Added By</th>
                                        <td>{{ $itemPrice->uploader->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Added On</th>
                                        <td>{{ $itemPrice->created_at->format('d-m-Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        @if ($itemPrice->description)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Description</h3>
                                        </div>
                                        <div class="card-body">
                                            {{ $itemPrice->description }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Price History</h3>
                                    </div>
                                    <div class="card-body table-responsive p-0">
                                        <table class="table table-hover text-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Price (IDR)</th>
                                                    <th>Start Date</th>
                                                    <th>Expiry Date</th>
                                                    <th>Added By</th>
                                                    <th>Added On</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($priceHistory as $history)
                                                    <tr>
                                                        <td>{{ number_format($history->price, 2) }}</td>
                                                        <td>{{ $history->start_date->format('d-m-Y') }}</td>
                                                        <td>{{ $history->expired_date ? $history->expired_date->format('d-m-Y') : 'N/A' }}
                                                        </td>
                                                        <td>{{ $history->creator->name ?? 'N/A' }}</td>
                                                        <td>{{ $history->created_at->format('d-m-Y H:i') }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">No price history found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('consignment.item-prices.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
