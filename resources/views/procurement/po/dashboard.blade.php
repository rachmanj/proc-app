@extends('layout.main')

@section('title_page')
    Purchase Order
@endsection

@section('breadcrumb_title')
    <small>
        procurement / purchase order / dashboard
    </small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-proc-po-links page="dashboard" />

            <!-- Summary Metrics Cards -->
            <div class="row mb-3">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($totalPOs ?? 0) }}</h3>
                            <p>Total Purchase Orders</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <a href="{{ route('procurement.po.index', ['page' => 'list']) }}" class="small-box-footer">
                            View All <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ number_format($totalDraftPOs ?? 0) }}</h3>
                            <p>Draft POs</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <a href="{{ route('procurement.po.index', ['page' => 'search']) }}&status[]=draft" class="small-box-footer">
                            View Drafts <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($totalSubmittedPOs ?? 0) }}</h3>
                            <p>Submitted POs</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                        <a href="{{ route('procurement.po.index', ['page' => 'search']) }}&status[]=submitted" class="small-box-footer">
                            View Submitted <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($totalApprovedPOs ?? 0) }}</h3>
                            <p>Approved POs</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="{{ route('procurement.po.index', ['page' => 'search']) }}&status[]=approved" class="small-box-footer">
                            View Approved <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Value Metrics Cards -->
            <div class="row mb-3">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>Rp {{ number_format($totalPOValue ?? 0, 0, ',', '.') }}</h3>
                            <p>Total PO Value</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>Rp {{ number_format($monthlyPOValue ?? 0, 0, ',', '.') }}</h3>
                            <p>Monthly PO Value</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($activeSuppliers ?? 0) }}</h3>
                            <p>Active Suppliers</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <a href="{{ route('suppliers.index') }}" class="small-box-footer">
                            View Suppliers <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row mb-3">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">PO Status Distribution</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="poStatusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('procurement.po.index', ['page' => 'create']) }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create New PO
                                </a>
                                <a href="{{ route('procurement.po.index', ['page' => 'search']) }}" class="btn btn-info">
                                    <i class="fas fa-search"></i> Search POs
                                </a>
                                <a href="{{ route('procurement.po.index', ['page' => 'list']) }}" class="btn btn-secondary">
                                    <i class="fas fa-list"></i> View All POs
                                </a>
                                <a href="{{ route('approvals.po.pending') }}" class="btn btn-warning">
                                    <i class="fas fa-clock"></i> Pending Approvals
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- General Info Table -->
            @include('procurement.po.dashboard._general_info')
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Load PO Status Chart
            loadPOStatusChart();

            function loadPOStatusChart() {
                const statusData = @json($statusDistribution ?? []);
                
                if (Object.keys(statusData).length === 0) {
                    $('#poStatusChart').parent().html('<p class="text-center text-muted">No data available</p>');
                    return;
                }

                // Prepare chart data
                const labels = [];
                const values = [];
                const colors = {
                    'draft': '#6c757d',
                    'submitted': '#ffc107',
                    'approved': '#28a745',
                    'revision': '#dc3545',
                    'rejected': '#dc3545'
                };

                Object.keys(statusData).forEach(status => {
                    labels.push(status.charAt(0).toUpperCase() + status.slice(1));
                    values.push(statusData[status]);
                });

                var ctx = document.getElementById('poStatusChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: labels.map(label => {
                                const status = label.toLowerCase();
                                return colors[status] || '#007bff';
                            })
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        legend: {
                            position: 'bottom'
                        },
                        tooltips: {
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    const label = data.labels[tooltipItem.index];
                                    const value = data.datasets[0].data[tooltipItem.index];
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
