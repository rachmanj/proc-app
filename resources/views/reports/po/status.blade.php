@extends('layout.main')

@section('title_page')
    PO Status Report
@endsection

@section('breadcrumb_title')
    <small>Reports / PO / Status Report</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>PO Status Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.po.status') }}" class="mb-4">
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

                    <!-- Summary -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total POs</span>
                                    <span class="info-box-number">{{ number_format($totalPos) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart and Table -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">PO Status Distribution</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="poStatusChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Status Breakdown</h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Status</th>
                                                <th class="text-right">Count</th>
                                                <th class="text-right">%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($statusCounts as $status => $count)
                                                <tr>
                                                    <td>{{ ucfirst($status) }}</td>
                                                    <td class="text-right">{{ number_format($count) }}</td>
                                                    <td class="text-right">{{ $totalPos > 0 ? number_format(($count / $totalPos) * 100, 1) : 0 }}%</td>
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
@endsection

@section('scripts')
    <script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
    <script>
        $(function() {
            const ctx = document.getElementById('poStatusChart').getContext('2d');
            const statusData = @json(array_values($statusCounts));
            const statusLabels = @json(array_keys($statusCounts));

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: statusLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                    datasets: [{
                        data: statusData,
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#ffc107',
                            '#dc3545',
                            '#6c757d'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        position: 'bottom'
                    }
                }
            });
        });
    </script>
@endsection
