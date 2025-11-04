@extends('layout.main')

@section('title_page')
    PO by Supplier Report
@endsection

@section('breadcrumb_title')
    <small>Reports / PO / By Supplier</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-truck mr-2"></i>PO by Supplier Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.po.by-supplier') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-filter mr-1"></i>Apply Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Chart and Table -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Top 20 Suppliers by Value</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="supplierChart" height="400"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Supplier Summary</h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Supplier</th>
                                                    <th class="text-right">PO Count</th>
                                                    <th class="text-right">Total Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($supplierData as $item)
                                                    <tr>
                                                        <td>{{ $item['supplier_name'] ?? 'Unknown' }}</td>
                                                        <td class="text-right">{{ number_format($item['count'] ?? 0) }}</td>
                                                        <td class="text-right">{{ number_format($item['total_value'] ?? 0, 2) }}</td>
                                                    </tr>
                                                @endforeach
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
@endsection

@section('scripts')
    <script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
    <script>
        $(function() {
            const ctx = document.getElementById('supplierChart').getContext('2d');
            const supplierDataRaw = @json($supplierData);
            const supplierData = supplierDataRaw.slice(0, 10).map(item => item.total_value);
            const supplierLabels = supplierDataRaw.slice(0, 10).map(item => item.supplier_name);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: supplierLabels,
                    datasets: [{
                        label: 'Total Value',
                        data: supplierData,
                        backgroundColor: '#007bff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        minimumFractionDigits: 0
                                    }).format(value);
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
