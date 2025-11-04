@extends('layout.main')

@section('title_page')
    PR Aging Report
@endsection

@section('breadcrumb_title')
    <small>Reports / PR / Aging Report</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock mr-2"></i>PR Aging Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Alert for Overdue -->
                    @if($overduePrs > 0)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Alert:</strong> {{ number_format($overduePrs) }} PR(s) are overdue (>30 days)
                        </div>
                    @endif

                    <!-- Chart -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">PR Aging Distribution</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="agingChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aging Table -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Aging Breakdown</h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Age Range</th>
                                                <th class="text-right">Count</th>
                                                <th class="text-right">Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $total = $agingData->sum('count');
                                            @endphp
                                            @foreach($agingData as $item)
                                                <tr>
                                                    <td>{{ $item->age_range }}</td>
                                                    <td class="text-right">{{ number_format($item->count) }}</td>
                                                    <td class="text-right">
                                                        {{ $total > 0 ? number_format(($item->count / $total) * 100, 1) : 0 }}%
                                                    </td>
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
            const ctx = document.getElementById('agingChart').getContext('2d');
            const agingData = @json($agingData->pluck('count')->toArray());
            const agingLabels = @json($agingData->pluck('age_range')->toArray());

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: agingLabels,
                    datasets: [{
                        label: 'Number of PRs',
                        data: agingData,
                        backgroundColor: [
                            '#28a745',
                            '#ffc107',
                            '#fd7e14',
                            '#dc3545',
                            '#6c757d'
                        ]
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
