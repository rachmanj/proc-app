@extends('layout.main')

@section('title_page')
    Approvals
@endsection

@section('breadcrumb_title')
    <small>approvals / purchase order / search</small>
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <x-aprv-po-links page='search' />

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Search Purchase Order</h3>
                </div>
                <div class="card-body">
                    <form id="search-form" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Document Number</label>
                                    <input type="text" class="form-control" id="doc_num" name="doc_num">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Supplier Name</label>
                                    <input type="text" class="form-control" id="supplier_name" name="supplier_name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" class="form-control" id="date_from" name="date_from">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" class="form-control" id="date_to" name="date_to">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-sm btn-primary">Search</button>
                                <button type="reset" class="btn btn-sm btn-secondary">Reset</button>
                            </div>
                        </div>
                    </form>

                    <table class="table table-bordered table-striped" id="search-results-table">
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
            // Initialize DataTable
            var table = $("#search-results-table").DataTable({
                processing: true,
                serverSide: true,
                deferLoading: false, // Prevents initial ajax request
                responsive: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('procurement.po.search') }}',
                    data: function(d) {
                        d.doc_num = $('#doc_num').val();
                        d.supplier_name = $('#supplier_name').val();
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
                        $('#search-results-table tbody').html(
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
            $('#search-results-table tbody').html(
                '<tr><td colspan="7" class="text-center">Please click search to view data</td></tr>'
            );

            // Handle search form submission
            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });

            // Handle reset button
            $('#search-form button[type="reset"]').click(function() {
                $(this).closest('form').find("input").val("");
                // Clear table and show initial message
                $('#search-results-table tbody').html(
                    '<tr><td colspan="7" class="text-center">Please click search to view data</td></tr>'
                );
            });
        });
    </script>
@endsection
