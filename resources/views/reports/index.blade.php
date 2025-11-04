@extends('layout.main')

@section('title_page')
    Reports & Analytics
@endsection

@section('breadcrumb_title')
    <small>Reports & Analytics</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i>Reports & Analytics</h3>
                </div>
                <div class="card-body">
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs" id="reportsTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pr-reports-tab" data-toggle="tab" href="#pr-reports" role="tab">
                                <i class="fas fa-file-alt mr-1"></i>PR Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="po-reports-tab" data-toggle="tab" href="#po-reports" role="tab">
                                <i class="fas fa-shopping-cart mr-1"></i>PO Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="approval-reports-tab" data-toggle="tab" href="#approval-reports" role="tab">
                                <i class="fas fa-check-circle mr-1"></i>Approval Reports
                            </a>
                        </li>
                    </ul>

                    <!-- Tabs Content -->
                    <div class="tab-content mt-3" id="reportsTabsContent">
                        <!-- PR Reports Tab -->
                        <div class="tab-pane fade show active" id="pr-reports" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-primary card-outline">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-chart-pie mr-2"></i>PR Status Report</h5>
                                            <p class="card-text">View PR distribution by status with date filters</p>
                                            <a href="{{ route('reports.pr.status') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-arrow-right mr-1"></i>View Report
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-warning card-outline">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-clock mr-2"></i>PR Aging Report</h5>
                                            <p class="card-text">Analyze PR aging and overdue items</p>
                                            <a href="{{ route('reports.pr.aging') }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-arrow-right mr-1"></i>View Report
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-info card-outline">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-building mr-2"></i>PR by Department</h5>
                                            <p class="card-text">PR distribution across departments</p>
                                            <a href="{{ route('reports.pr.by-department') }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-arrow-right mr-1"></i>View Report
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-success card-outline">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-project-diagram mr-2"></i>PR by Project</h5>
                                            <p class="card-text">PR breakdown by project code</p>
                                            <a href="{{ route('reports.pr.by-project') }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-arrow-right mr-1"></i>View Report
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-secondary card-outline">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-hourglass-half mr-2"></i>PR Approval Time</h5>
                                            <p class="card-text">Analysis of PR approval timelines</p>
                                            <a href="{{ route('reports.pr.approval-time') }}" class="btn btn-secondary btn-sm">
                                                <i class="fas fa-arrow-right mr-1"></i>View Report
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PO Reports Tab -->
                        <div class="tab-pane fade" id="po-reports" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-primary card-outline">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-chart-pie mr-2"></i>PO Status Report</h5>
                                            <p class="card-text">View PO distribution by status</p>
                                            <a href="{{ route('reports.po.status') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-arrow-right mr-1"></i>View Report
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-success card-outline">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-money-bill-wave mr-2"></i>PO Value Report</h5>
                                            <p class="card-text">PO value trends over time</p>
                                            <a href="{{ route('reports.po.value') }}" class="btn btn-success btn-sm">
                                                <i class="fas fa-arrow-right mr-1"></i>View Report
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-info card-outline">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-truck mr-2"></i>PO by Supplier</h5>
                                            <p class="card-text">PO analysis by supplier</p>
                                            <a href="{{ route('reports.po.by-supplier') }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-arrow-right mr-1"></i>View Report
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-warning card-outline">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-shipping-fast mr-2"></i>Delivery Status</h5>
                                            <p class="card-text">PO delivery status tracking</p>
                                            <a href="{{ route('reports.po.delivery-status') }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-arrow-right mr-1"></i>View Report
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Reports Tab -->
                        <div class="tab-pane fade" id="approval-reports" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-primary card-outline">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-stopwatch mr-2"></i>Approval Time Analysis</h5>
                                            <p class="card-text">Average approval time by level</p>
                                            <a href="{{ route('reports.approval.time-analysis') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-arrow-right mr-1"></i>View Report
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-info card-outline">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-user-check mr-2"></i>Approval by Approver</h5>
                                            <p class="card-text">Approval statistics by approver</p>
                                            <a href="{{ route('reports.approval.by-approver') }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-arrow-right mr-1"></i>View Report
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card card-danger card-outline">
                                        <div class="card-body">
                                            <h5 class="card-title"><i class="fas fa-exclamation-triangle mr-2"></i>Bottleneck Analysis</h5>
                                            <p class="card-text">Identify approval bottlenecks</p>
                                            <a href="{{ route('reports.approval.bottleneck') }}" class="btn btn-danger btn-sm">
                                                <i class="fas fa-arrow-right mr-1"></i>View Report
                                            </a>
                                        </div>
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
