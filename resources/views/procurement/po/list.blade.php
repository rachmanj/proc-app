@extends('layout.main')

@section('title_page')
    Purchase Order
@endsection

@section('breadcrumb_title')
    <small>
        procurement / purchase order / list
    </small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-proc-po-links page="list" />

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Order List</h3>
                </div>
                <div class="card-body">
                    <table id="po-table" class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Document Number</th>
                                <th>Document Date</th>
                                <th>PR Number</th>
                                <th>Supplier Name</th>
                                <th>Create Date</th>
                                <th class="text-center">Day</th>
                                <th>Status</th>
                                <th>Action</th>
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
            let table = $('#po-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: '{{ route('procurement.po.data') }}',
                searching: true,
                paging: true,
                columns: [{
                        data: 'doc_num',
                        name: 'doc_num'
                    },
                    {
                        data: 'doc_date',
                        name: 'doc_date'
                    },
                    {
                        data: 'pr_no',
                        name: 'pr_no'
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
                        data: 'day',
                        name: 'day',
                        className: 'text-center',
                        type: 'num'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            return data;
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [5, 'desc'] // Order by Day column (highest first)
                ],
                columnDefs: [
                    {
                        targets: 5, // Day column
                        type: 'num' // Ensure numeric sorting
                    }
                ],
                stateSave: false,
                // Debug: log the order parameter
                drawCallback: function(settings) {
                    console.log('DataTables order:', settings.aaSorting);
                }
            });

            // Handle delete button click
            $('#po-table').on('click', '.delete-po', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('procurement/po') }}/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    );
                                    table.ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'Failed to delete Purchase Order.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
