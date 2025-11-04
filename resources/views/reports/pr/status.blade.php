@extends('layout.main')

@section('title_page')
    PR Status Report
@endsection

@section('breadcrumb_title')
    <small>Reports / PR / Status Report</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i>PR Status Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.pr.status') }}" class="mb-4">
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

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total PRs</span>
                                    <span class="info-box-number">{{ number_format($totalPrs) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">PR Status Distribution</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="prStatusChart" height="300"></canvas>
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
                                                    <td>{{ $status }}</td>
                                                    <td class="text-right">{{ number_format($count) }}</td>
                                                    <td class="text-right">{{ $totalPrs > 0 ? number_format(($count / $totalPrs) * 100, 1) : 0 }}%</td>
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
            const ctx = document.getElementById('prStatusChart').getContext('2d');
            const statusData = @json(array_values($statusCounts));
            const statusLabels = @json(array_keys($statusCounts));

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: [
                            '#007bff',
                            '#28a745',
                            '#ffc107',
                            '#dc3545',
                            '#6c757d',
                            '#17a2b8',
                            '#6610f2'
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
