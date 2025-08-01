@extends('layout.main')

@section('title_page')
    Purchase Request List
@endsection

@section('breadcrumb_title')
    <small>procurement / purchase request / list</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-proc-pr-links page='list' />

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Request List</h3>
                </div>
                <div class="card-body">
                    <table id="pr-table" class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>PR Number</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Project Code</th>
                                <th>For Unit</th>
                                <th class="text-center">Days</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                    </table>
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
    <!-- jQuery should be loaded first if not already included in your layout -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>

    <!-- Then load DataTables -->
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            let table = $('#pr-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: '{{ route('procurement.pr.data') }}',
                columns: [{
                        data: 'pr_no',
                        name: 'pr_no'
                    },
                    {
                        data: 'priority',
                        name: 'priority'
                    },
                    {
                        data: 'pr_status',
                        name: 'pr_status',
                        render: function(data) {
                            let badgeClass = 'badge-warning';
                            if (data === 'OPEN') {
                                badgeClass = 'badge-success';
                            } else if (data === 'CLOSED') {
                                badgeClass = 'badge-secondary';
                            } else if (data === 'progress') {
                                badgeClass = 'badge-info';
                            } else if (data === 'approved') {
                                badgeClass = 'badge-primary';
                            }
                            return `<span class="badge ${badgeClass}">${data}</span>`;
                        }
                        
                    },
                    {
                        data: 'pr_type',
                        name: 'pr_type'
                    },
                    {
                        data: 'project_code',
                        name: 'project_code'
                    },
                    {
                        data: 'for_unit',
                        name: 'for_unit'
                    },
                    {
                        data: 'day',
                        name: 'day',
                        className: 'text-center'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [6, 'desc']
                ]
            });
        });
    </script>
@endsection
