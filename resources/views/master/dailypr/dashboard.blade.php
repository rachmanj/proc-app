@extends('layout.main')

@section('title_page')
    Daily PR
@endsection

@section('breadcrumb_title')
    <small>
        master / daily pr / dashboard
    </small>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-8">
                        <h4>PR Temporary Data</h4>
                    </div>
                    <div class="col-4 text-right">
                        <button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="modal"
                            data-target="#importModal">
                            <i class="fas fa-file-import mr-2"></i>Import
                        </button>
                        <button type="button" class="btn btn-sm btn-success" id="importToPRBtn">
                            <i class="fas fa-file-import mr-2"></i>Import to PR Table
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="prTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>PR No</th>
                            <th>PR Date</th>
                            <th>Project</th>
                            <th>Department</th>
                            <th>Item Name</th>
                            <th>Qty</th>
                            <th>UOM</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import PR Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="importForm" method="POST" action="{{ route('master.dailypr.import') }}"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="file">Choose Excel File</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx, .xls"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Set up CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable
            var table = $('#prTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('master.dailypr.data') }}",
                columns: [{
                        data: 'pr_no',
                        name: 'pr_no'
                    },
                    {
                        data: 'pr_date',
                        name: 'pr_date'
                    },
                    {
                        data: 'project_code',
                        name: 'project_code'
                    },
                    {
                        data: 'dept_name',
                        name: 'dept_name'
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'uom',
                        name: 'uom'
                    },
                    {
                        data: 'pr_status',
                        name: 'pr_status'
                    }
                ]
            });

            // Handle Import to PR Table
            $('#importToPRBtn').on('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will import the temporary data to the PR table.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, import it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Importing...',
                            text: 'Please wait while we process your request.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('master.dailypr.import-to-pr') }}",
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.reload_page) {
                                    // Reload page to show flash message
                                    window.location.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.message,
                                        position: 'center'
                                    }).then(() => {
                                        table.ajax.reload();
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                if (xhr.responseJSON && xhr.responseJSON.reload_page) {
                                    // Reload page to show flash message
                                    window.location.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred while importing the data.',
                                        position: 'center'
                                    });
                                }
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
