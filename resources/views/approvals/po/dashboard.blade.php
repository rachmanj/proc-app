@extends('layout.main')

@section('title_page')
    Approvals Dashboard
@endsection

@section('breadcrumb_title')
    <small>
        approvals / purchase order / dashboard
    </small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-aprv-po-links page="dashboard" />

            <div class="row mb-4">
                <!-- Pending Approvals Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $pendingCount ?? 0 }}</h3>
                            <p>Pending Approvals</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="{{ route('approvals.po.pending') }}" class="small-box-footer">
                            View All Pending <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Approved Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $approvedCount ?? 0 }}</h3>
                            <p>Approved by You</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="#approved" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Rejected Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $rejectedCount ?? 0 }}</h3>
                            <p>Rejected by You</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <a href="#rejected" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Revision Requested Card -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ $revisionCount ?? 0 }}</h3>
                            <p>Revision Requested</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <a href="#revisions" class="small-box-footer">
                            View Details <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Approvals Table -->
            <div class="card" id="pending-approvals">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-1"></i>
                        Pending Purchase Orders Requiring Your Approval
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="pending-table">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th>Project</th>
                                <th>Approval Level</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Will be filled by AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-1"></i>
                        Recent Approval Activity
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($recentActivity ?? [] as $activity)
                            <div>
                                <i
                                    class="fas fa-{{ $activity->status === 'approved'
                                        ? 'check bg-success'
                                        : ($activity->status === 'rejected'
                                            ? 'times bg-danger'
                                            : ($activity->status === 'revision'
                                                ? 'edit bg-info'
                                                : 'clock bg-warning')) }}"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i>
                                        {{ $activity->created_at->diffForHumans() }}</span>
                                    <h3 class="timeline-header">
                                        <a href="{{ route('approvals.po.show', $activity->purchase_order_id) }}">PO
                                            #{{ $activity->purchase_order->doc_num }}</a>
                                    </h3>
                                    <div class="timeline-body">
                                        @if ($activity->status === 'revision')
                                            Revision Requested - {{ $activity->notes ?? 'No notes provided' }}
                                        @else
                                            {{ ucfirst($activity->status) }} -
                                            {{ $activity->notes ?? 'No notes provided' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div>
                                <i class="fas fa-info bg-info"></i>
                                <div class="timeline-item">
                                    <div class="timeline-body">
                                        No recent activity found.
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection

@section('scripts')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(function() {
            $('#pending-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('approvals.po.pending-data') }}",
                columns: [{
                        data: 'doc_num',
                        name: 'doc_num',
                        searchable: true
                    },
                    {
                        data: 'doc_date',
                        name: 'doc_date',
                        searchable: false
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier_name',
                        searchable: false
                    },
                    {
                        data: 'project_code',
                        name: 'project_code',
                        searchable: true
                    },
                    {
                        data: 'approval_level',
                        name: 'approval_level',
                        searchable: false
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ]
            });
        });
    </script>
@endsection
