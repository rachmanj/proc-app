@extends('layout.main')

@section('title_page')
    Approval Bottleneck Report
@endsection

@section('breadcrumb_title')
    <small>Reports / Approval / Bottleneck</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-exclamation-triangle mr-2"></i>Approval Bottleneck Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.approval.bottleneck') }}" class="mb-4">
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
                                    <h3 class="card-title">Pending Approvals by Level</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="bottleneckChart" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Bottleneck Analysis</h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Level</th>
                                                <th class="text-right">Pending</th>
                                                <th class="text-right">Avg Wait (hrs)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($bottleneckData as $item)
                                                <tr>
                                                    <td>{{ $item['level_name'] }}</td>
                                                    <td class="text-right"><span class="badge badge-danger">{{ number_format($item['pending_count']) }}</span></td>
                                                    <td class="text-right">{{ number_format($item['avg_waiting_hours'], 2) }}</td>
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
            const ctx = document.getElementById('bottleneckChart').getContext('2d');
            const bottleneckData = @json($bottleneckData->pluck('pending_count')->toArray());
            const bottleneckLabels = @json($bottleneckData->pluck('level_name')->toArray());

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: bottleneckLabels,
                    datasets: [{
                        label: 'Pending Approvals',
                        data: bottleneckData,
                        backgroundColor: '#dc3545'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endsection
