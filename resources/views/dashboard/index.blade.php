@extends('layout.main')

@section('title', 'Dashboard')
@section('title_page', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Metrics Cards -->
        <div class="row" id="metrics-cards">
            <!-- PR Cards -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="pr-total">-</h3>
                        <p>Total Purchase Requests</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <a href="{{ route('procurement.pr.index') }}" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="pr-open">-</h3>
                        <p>Open PRs</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <a href="{{ route('procurement.pr.index') }}" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="pr-approved">-</h3>
                        <p>Approved PRs</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <a href="{{ route('procurement.pr.index') }}" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- PO Cards -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3 id="po-total">-</h3>
                        <p>Total Purchase Orders</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <a href="{{ route('procurement.po.index') }}" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="po-submitted">-</h3>
                        <p>Submitted POs</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <a href="{{ route('procurement.po.index') }}" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="po-approved">-</h3>
                        <p>Approved POs</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <a href="{{ route('procurement.po.index') }}" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="pending-approvals">-</h3>
                        <p>Pending Approvals</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="{{ route('approvals.po.index') }}" class="small-box-footer">
                        View Pending <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="avg-approval-time">-</h3>
                        <p>Avg Approval Time (hours)</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="po-value-monthly">-</h3>
                        <p>Monthly PO Value</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3 id="active-suppliers">-</h3>
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

            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="items-consignment">-</h3>
                        <p>Items in Consignment</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <a href="{{ route('consignment.item-prices.index') }}" class="small-box-footer">
                        View Items <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('procurement.pr.index') }}" class="btn btn-primary mr-2">
                            <i class="fas fa-file-alt"></i> Goto PR Page
                        </a>
                        <a href="{{ route('procurement.po.index') }}" class="btn btn-success mr-2">
                            <i class="fas fa-shopping-cart"></i> Goto PO Page
                        </a>
                        @if ($isApprover)
                            <a href="{{ route('approvals.po.pending') }}" class="btn btn-warning mr-2">
                                <i class="fas fa-clipboard-check"></i> View Pending Approvals
                            </a>
                        @endif
                        <a href="{{ route('procurement.pr.index') }}" class="btn btn-info">
                            <i class="fas fa-search"></i> Quick Search
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">PR Status Distribution</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="prStatusChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">PO Trend (Last 30 Days)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="poTrendChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Approval Time Analysis</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="approvalTimeChart"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Suppliers Chart - Full Width -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Top 20 Suppliers by PO Value</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="topSuppliersChart"
                            style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department PR Chart -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Department-wise PR Distribution</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="departmentPrChart"
                            style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Feed -->
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Purchase Requests</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="products-list product-list-in-card pl-2 pr-2" id="recent-prs">
                            <li class="text-center p-3">Loading...</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Purchase Orders</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="products-list product-list-in-card pl-2 pr-2" id="recent-pos">
                            <li class="text-center p-3">Loading...</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Approvals</h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="products-list product-list-in-card pl-2 pr-2" id="recent-approvals">
                            <li class="text-center p-3">Loading...</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if ($isApprover)
            <!-- My Tasks Widget for Approvers -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-tasks"></i> My Tasks - Pending Approvals</h3>
                            <div class="card-tools">
                                <a href="{{ route('approvals.po.pending') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-list"></i> View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="pending-approvals-table">
                                    <thead>
                                        <tr>
                                            <th>PO Number</th>
                                            <th>Supplier</th>
                                            <th>Approval Level</th>
                                            <th>Waiting Since</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <small class="text-muted">Showing up to 10 pending approvals. Click "View All" to see more.</small>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            loadMetrics();
            loadCharts();
            loadActivity();

            function loadMetrics() {
                $.ajax({
                    url: '{{ route('api.dashboard.metrics') }}',
                    method: 'GET',
                    success: function(data) {
                        $('#pr-total').text(data.pr.total);
                        $('#pr-open').text(data.pr.open);
                        $('#pr-approved').text(data.pr.approved);
                        $('#po-total').text(data.po.total);
                        $('#po-submitted').text(data.po.submitted);
                        $('#po-approved').text(data.po.approved);
                        $('#pending-approvals').text(data.pending_approvals);
                        $('#avg-approval-time').text(data.average_approval_time);
                        $('#po-value-monthly').text(formatCurrency(data.po_value.monthly));
                        $('#active-suppliers').text(data.active_suppliers);
                        $('#items-consignment').text(data.items_in_consignment);
                    }
                });
            }

            function loadCharts() {
                loadPRStatusChart();
                loadPOTrendChart();
                loadApprovalTimeChart();
                loadTopSuppliersChart();
                loadDepartmentPrChart();
            }

            function loadPRStatusChart() {
                $.ajax({
                    url: '{{ route('api.dashboard.charts.pr-status') }}',
                    method: 'GET',
                    success: function(data) {
                        var ctx = document.getElementById('prStatusChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    data: data.values,
                                    backgroundColor: data.colors
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                responsive: true,
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        });
                    }
                });
            }

            function loadPOTrendChart() {
                $.ajax({
                    url: '{{ route('api.dashboard.charts.po-trend') }}',
                    method: 'GET',
                    success: function(data) {
                        var ctx = document.getElementById('poTrendChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: data.datasets
                            },
                            options: {
                                maintainAspectRatio: false,
                                responsive: true,
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                },
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            }
                        });
                    }
                });
            }

            function loadApprovalTimeChart() {
                $.ajax({
                    url: '{{ route('api.dashboard.charts.approval-time') }}',
                    method: 'GET',
                    success: function(data) {
                        if (data.labels.length === 0) {
                            $('#approvalTimeChart').parent().html(
                                '<p class="text-center p-3">No approval data available</p>');
                            return;
                        }
                        var ctx = document.getElementById('approvalTimeChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Average Hours',
                                    data: data.values,
                                    backgroundColor: '#007bff'
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                responsive: true,
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                }
                            }
                        });
                    }
                });
            }

            function loadTopSuppliersChart() {
                $.ajax({
                    url: '{{ route('api.dashboard.charts.top-suppliers') }}',
                    method: 'GET',
                    success: function(data) {
                        if (data.labels.length === 0) {
                            $('#topSuppliersChart').parent().html(
                                '<p class="text-center p-3">No supplier data available</p>');
                            return;
                        }
                        var ctx = document.getElementById('topSuppliersChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data.labels,
                                datasets: [{
                                    label: 'Total Value (IDR)',
                                    data: data.values,
                                    backgroundColor: '#28a745'
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                responsive: true,
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true,
                                            callback: function(value) {
                                                return formatCurrency(value);
                                            }
                                        }
                                    }]
                                }
                            }
                        });
                    }
                });
            }

            function loadDepartmentPrChart() {
                $.ajax({
                    url: '{{ route('api.dashboard.charts.department-pr') }}',
                    method: 'GET',
                    success: function(data) {
                        if (data.labels.length === 0) {
                            $('#departmentPrChart').parent().html(
                                '<p class="text-center p-3">No department data available</p>');
                            return;
                        }
                        var ctx = document.getElementById('departmentPrChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data.labels,
                                datasets: data.datasets
                            },
                            options: {
                                maintainAspectRatio: false,
                                responsive: true,
                                scales: {
                                    xAxes: [{
                                        stacked: true
                                    }],
                                    yAxes: [{
                                        stacked: true,
                                        ticks: {
                                            beginAtZero: true
                                        }
                                    }]
                                },
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            }
                        });
                    }
                });
            }

            function loadActivity() {
                $.ajax({
                    url: '{{ route('api.dashboard.activity') }}',
                    method: 'GET',
                    success: function(data) {
                        renderRecentPRs(data.recent_prs);
                        renderRecentPOs(data.recent_pos);
                        renderRecentApprovals(data.recent_approvals);
                        if (data.pending_approvals && data.pending_approvals.length > 0) {
                            renderPendingApprovals(data.pending_approvals);
                        }
                    }
                });
            }

            function renderRecentPRs(prs) {
                var html = '';
                if (prs.length === 0) {
                    html = '<li class="text-center p-3">No recent PRs</li>';
                } else {
                    prs.forEach(function(pr) {
                        html += '<li class="item"><div class="product-info"><a href="' + pr.url +
                            '" class="product-title">' + pr.pr_no +
                            '<span class="badge badge-info float-right">' + pr.status +
                            '</span></a><span class="product-description">' + pr.department + ' - ' + pr
                            .requestor + '<br><small>' + pr.created_at + '</small></span></div></li>';
                    });
                }
                $('#recent-prs').html(html);
            }

            function renderRecentPOs(pos) {
                var html = '';
                if (pos.length === 0) {
                    html = '<li class="text-center p-3">No recent POs</li>';
                } else {
                    pos.forEach(function(po) {
                        html += '<li class="item"><div class="product-info"><a href="' + po.url +
                            '" class="product-title">' + po.doc_num +
                            '<span class="badge badge-success float-right">' + po.status +
                            '</span></a><span class="product-description">' + po.supplier +
                            '<br><small>Value: ' + po.total_value + '</small><br><small>' + po.created_at +
                            '</small></span></div></li>';
                    });
                }
                $('#recent-pos').html(html);
            }

            function renderRecentApprovals(approvals) {
                var html = '';
                if (approvals.length === 0) {
                    html = '<li class="text-center p-3">No recent approvals</li>';
                } else {
                    approvals.forEach(function(approval) {
                        html += '<li class="item"><div class="product-info"><a href="' + approval.url +
                            '" class="product-title">' + approval.po_number +
                            '<span class="badge badge-success float-right">Approved</span></a><span class="product-description">' +
                            approval.level + '<br><small>' + approval.approved_at +
                            '</small></span></div></li>';
                    });
                }
                $('#recent-approvals').html(html);
            }

            function renderPendingApprovals(approvals) {
                var html = '';
                if (approvals.length === 0) {
                    html = '<tr><td colspan="5" class="text-center text-muted">No pending approvals</td></tr>';
                } else {
                    approvals.forEach(function(approval) {
                        var waitingTime = getTimeAgo(approval.created_at);
                        html += '<tr>' +
                            '<td><a href="' + approval.url + '" class="font-weight-bold">' + approval.po_number + '</a></td>' +
                            '<td>' + (approval.supplier || 'N/A') + '</td>' +
                            '<td><span class="badge badge-warning">' + approval.level + '</span></td>' +
                            '<td><small>' + waitingTime + '<br>' + approval.created_at + '</small></td>' +
                            '<td><a href="' + approval.url + '" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> Review</a></td>' +
                            '</tr>';
                    });
                }
                $('#pending-approvals-table tbody').html(html);
            }

            function getTimeAgo(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const seconds = Math.floor((now - date) / 1000);
                
                if (seconds < 60) return 'Just now';
                if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
                if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
                if (seconds < 604800) return Math.floor(seconds / 86400) + ' days ago';
                return date.toLocaleDateString();
            }

            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(value);
            }

        });

        // Scroll to top button functionality (outside document ready to ensure button exists)
        document.addEventListener('DOMContentLoaded', function() {
            const scrollToTopBtn = document.getElementById('scrollToTopBtn');
            
            if (scrollToTopBtn) {
                // Show/hide button based on scroll position
                window.addEventListener('scroll', function() {
                    if (window.pageYOffset > 300) {
                        scrollToTopBtn.classList.add('show');
                    } else {
                        scrollToTopBtn.classList.remove('show');
                    }
                });

                // Smooth scroll to top when button is clicked
                scrollToTopBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
        });
    </script>

    <style>
        #scrollToTopBtn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s, visibility 0.3s, transform 0.3s;
            z-index: 1000;
        }

        #scrollToTopBtn:hover {
            background-color: #0056b3;
            transform: translateY(-3px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        }

        #scrollToTopBtn:active {
            transform: translateY(-1px);
        }

        #scrollToTopBtn.show {
            opacity: 1;
            visibility: visible;
        }

        @media (max-width: 768px) {
            #scrollToTopBtn {
                bottom: 20px;
                right: 20px;
                width: 45px;
                height: 45px;
                font-size: 18px;
            }
        }
    </style>

    <!-- Floating Scroll to Top Button -->
    <button id="scrollToTopBtn" title="Scroll to top">
        <i class="fas fa-arrow-up"></i>
    </button>
@endsection
