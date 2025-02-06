@extends('layout.main')

@section('title_page')
    Purchase Order
@endsection

@section('breadcrumb_title')
    <small>procurement / purchase order / search</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-proc-po-links page='search' />

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Search Purchase Order</h3>
                </div>
                <div class="card-body">
                    <form id="search-form">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="doc_num">PO Number</label>
                                <input type="text" class="form-control" id="doc_num" name="doc_num">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="pr_num">PR Number</label>
                                <input type="text" class="form-control" id="pr_num" name="pr_num">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="supplier_name">Supplier</label>
                                <select class="form-control select2" id="supplier_name" name="supplier_name">
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier }}">{{ $supplier }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="unit_no">Unit No</label>
                                <select class="form-control select2" id="unit_no" name="unit_no">
                                    <option value="">Select Unit No</option>
                                    @foreach ($unitNos as $unitNo)
                                        <option value="{{ $unitNo }}">{{ $unitNo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="project_code">Project Code</label>
                                <select class="form-control select2" id="project_code" name="project_code">
                                    <option value="">Select Project Code</option>
                                    @foreach ($projectCodes as $projectCode)
                                        <option value="{{ $projectCode }}">{{ $projectCode }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="status">Status</label>
                                <select class="form-control select2" id="status" name="status">
                                    <option value="">Select Status</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="date_from">Date From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="date_to">Date To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </form>

                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="po-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Document Number</th>
                                            <th>Document Date</th>
                                            <th>Supplier Name</th>
                                            <th>Create Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Results will be inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('scripts')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2 with Bootstrap 4 theme
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Initialize DataTable
            let table = $('#po-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('procurement.po.search') }}',
                    data: function(d) {
                        d.doc_num = $('#doc_num').val();
                        d.pr_num = $('#pr_num').val();
                        d.supplier_name = $('#supplier_name').val();
                        d.unit_no = $('#unit_no').val();
                        d.project_code = $('#project_code').val();
                        d.status = $('#status').val();
                        d.date_from = $('#date_from').val();
                        d.date_to = $('#date_to').val();
                    },
                    error: function(xhr, error, thrown) {
                        let errorMessage = 'Error occurred while searching';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage
                        });

                        // Clear table and show error message
                        $('#po-table tbody').html(
                            '<tr><td colspan="7" class="text-center text-danger">Error loading data. Please try again.</td></tr>'
                        );
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'doc_num',
                        name: 'doc_num'
                    },
                    {
                        data: 'doc_date',
                        name: 'doc_date'
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier_name'
                    },
                    {
                        data: 'create_date',
                        name: 'create_date'
                    },
                    {
                        data: 'status',
                        name: 'status'
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

            // Show initial message
            $('#po-table tbody').html(
                '<tr><td colspan="7" class="text-center">Please click search to view data</td></tr>'
            );

            // Handle form submission
            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });

            // Handle form reset
            $('#search-form').on('reset', function(e) {
                $('.select2').val('').trigger('change');
                setTimeout(function() {
                    table.draw();
                }, 100);
            });
        });
    </script>
@endsection
