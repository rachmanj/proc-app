@extends('layout.main')

@section('title', 'Price History')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.dashboard') }}">Consignment</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.search') }}">Search</a></li>
    <li class="breadcrumb-item active">Price History</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Item Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 30%">Item Code</th>
                                        <td>{{ $itemCode }}</td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{{ $itemInfo->item_description ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Part Number</th>
                                        <td>{{ $itemInfo->part_number ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Brand</th>
                                        <td>{{ $itemInfo->brand ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="width: 30%">Supplier</th>
                                        <td>{{ $itemInfo->supplier->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Current Price</th>
                                        <td>{{ $itemInfo ? number_format($itemInfo->price, 2) . ' IDR' : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>UOM</th>
                                        <td>{{ $itemInfo->uom ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Current Start Date</th>
                                        <td>{{ $itemInfo && $itemInfo->start_date ? $itemInfo->start_date->format('d-m-Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Price History Timeline</h3>
                    </div>
                    <div class="card-body">
                        <div id="price-chart" style="height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Price History Details</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Price (IDR)</th>
                                    <th>Start Date</th>
                                    <th>Expiry Date</th>
                                    <th>Supplier</th>
                                    <th>Warehouse</th>
                                    <th>Project</th>
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
                                        <td>{{ $history->supplier->name ?? 'N/A' }}</td>
                                        <td>{{ $history->warehouse }}</td>
                                        <td>{{ $history->project }}</td>
                                        <td>{{ $history->creator->name ?? 'N/A' }}</td>
                                        <td>{{ $history->created_at->format('d-m-Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No price history found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $priceHistory->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Extract data from the price history
            const priceData = @json(
                $priceHistory->map(function ($item) {
                    return [$item->start_date->format('Y-m-d'), $item->price];
                }));

            if (priceData.length > 0) {
                // Sort by date
                priceData.sort((a, b) => new Date(a[0]) - new Date(b[0]));

                const dates = priceData.map(item => item[0]);
                const prices = priceData.map(item => item[1]);

                const options = {
                    series: [{
                        name: 'Price (IDR)',
                        data: prices
                    }],
                    chart: {
                        height: 300,
                        type: 'line',
                        zoom: {
                            enabled: true
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'straight'
                    },
                    title: {
                        text: 'Price History Over Time',
                        align: 'left'
                    },
                    grid: {
                        row: {
                            colors: ['#f3f3f3', 'transparent'],
                            opacity: 0.5
                        },
                    },
                    xaxis: {
                        categories: dates,
                        title: {
                            text: 'Date'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Price (IDR)'
                        },
                        min: Math.min(...prices) * 0.9,
                        max: Math.max(...prices) * 1.1
                    },
                    tooltip: {
                        y: {
                            formatter: function(value) {
                                return value.toLocaleString('id-ID') + ' IDR';
                            }
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector("#price-chart"), options);
                chart.render();
            } else {
                document.getElementById('price-chart').innerHTML =
                    '<div class="text-center p-5">No price history data available to display chart</div>';
            }
        });
    </script>
@endsection
