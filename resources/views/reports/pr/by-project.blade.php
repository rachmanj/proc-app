@extends('layout.main')

@section('title_page')
    PR by Project Report
@endsection

@section('breadcrumb_title')
    <small>Reports / PR / By Project</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-project-diagram mr-2"></i>PR by Project Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.pr.by-project') }}" class="mb-4">
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
                                    <h3 class="card-title">Top 20 Projects by PR Count</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="projectChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Project Breakdown</h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Project Code</th>
                                                    <th class="text-right">PR Count</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($projectData as $item)
                                                    <tr>
                                                        <td>{{ $item->project_code ?: 'Unknown' }}</td>
                                                        <td class="text-right">{{ number_format($item->count) }}</td>
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
            const ctx = document.getElementById('projectChart').getContext('2d');
            const projectData = @json($projectData->take(10)->pluck('count')->toArray());
            const projectLabels = @json($projectData->take(10)->pluck('project_code')->toArray());

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: projectLabels,
                    datasets: [{
                        label: 'PR Count',
                        data: projectData,
                        backgroundColor: '#007bff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    indexAxis: 'y'
                }
            });
        });
    </script>
@endsection
