@extends('layout.main')

@section('title_page')
    Purchase Request
@endsection

@section('breadcrumb_title')
    <small>procurement / purchase request / search</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-proc-pr-links page='search' />

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Search Purchase Request</h3>
                </div>
                <div class="card-body">
                    <form id="search-form" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>PR Number</label>
                                    <input type="text" class="form-control" id="pr_no" name="pr_no"
                                        value="{{ $searchParams['pr_no'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>PR Draft Number</label>
                                    <input type="text" class="form-control" id="pr_draft_no" name="pr_draft_no"
                                        value="{{ $searchParams['pr_draft_no'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>PR Revision Number</label>
                                    <input type="text" class="form-control" id="pr_rev_no" name="pr_rev_no"
                                        value="{{ $searchParams['pr_rev_no'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Priority</label>
                                    <select class="form-control" id="priority" name="priority">
                                        <option value="">All</option>
                                        @foreach ($priorities as $priority)
                                            <option value="{{ $priority }}"
                                                {{ ($searchParams['priority'] ?? '') == $priority ? 'selected' : '' }}>
                                                {{ $priority }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control" id="pr_status" name="pr_status">
                                        <option value="">All</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}"
                                                {{ ($searchParams['pr_status'] ?? '') == $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select class="form-control" id="pr_type" name="pr_type">
                                        <option value="">All</option>
                                        @foreach ($types as $type)
                                            <option value="{{ $type }}"
                                                {{ ($searchParams['pr_type'] ?? '') == $type ? 'selected' : '' }}>
                                                {{ $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Project Code</label>
                                    <select class="form-control" id="project_code" name="project_code">
                                        <option value="">All</option>
                                        @foreach ($projectCodes as $code)
                                            <option value="{{ $code }}"
                                                {{ ($searchParams['project_code'] ?? '') == $code ? 'selected' : '' }}>
                                                {{ $code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>For Unit</label>
                                    <select class="form-control" id="for_unit" name="for_unit">
                                        <option value="">All</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit }}"
                                                {{ ($searchParams['for_unit'] ?? '') == $unit ? 'selected' : '' }}>
                                                {{ $unit }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                <th>PR Number</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Project Code</th>
                                <th>For Unit</th>
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
            // Function to initialize DataTable
            function initializeDataTable() {
                // Clear any existing DataTable
                if ($.fn.DataTable.isDataTable('#search-results-table')) {
                    $('#search-results-table').DataTable().destroy();
                }

                // Clear the table body first
                $('#search-results-table tbody').empty();

                return $("#search-results-table").DataTable({
                    processing: true,
                    serverSide: true,
                    deferLoading: false,
                    responsive: true,
                    autoWidth: false,
                    dom: 'rt<"bottom"ip>', // Hide default search and length changing
                    ajax: {
                        url: '{{ route('procurement.pr.search') }}',
                        data: function(d) {
                            d.pr_no = $('#pr_no').val();
                            d.pr_draft_no = $('#pr_draft_no').val();
                            d.pr_rev_no = $('#pr_rev_no').val();
                            d.priority = $('#priority').val();
                            d.pr_status = $('#pr_status').val();
                            d.pr_type = $('#pr_type').val();
                            d.project_code = $('#project_code').val();
                            d.for_unit = $('#for_unit').val();
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

                            $('#search-results-table tbody').html(
                                '<tr><td colspan="8" class="text-center text-danger">Error loading data. Please try again.</td></tr>'
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
                            data: 'pr_no',
                            name: 'pr_no'
                        },
                        {
                            data: 'priority',
                            name: 'priority'
                        },
                        {
                            data: 'pr_status',
                            name: 'pr_status'
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
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [1, 'desc']
                    ],
                    language: {
                        emptyTable: "Please click search to view data"
                    }
                });
            }

            // Initialize DataTable
            var table = initializeDataTable();

            // Clear DataTable and show initial message
            function showInitialMessage() {
                $('.dataTables_processing').hide();
                $('#search-results-table tbody').html(
                    '<tr><td colspan="8" class="text-center">Please click search to view data</td></tr>'
                );
            }

            // Show initial message on first load if no search parameters
            @if (!empty($searchParams))
                table.draw();
            @else
                showInitialMessage();
            @endif

            // Handle search form submission
            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });

            // Handle reset button
            $('#search-form button[type="reset"]').click(function(e) {
                e.preventDefault();

                // Reset all input fields
                $('#search-form input').val('');

                // Reset all select fields
                $('#search-form select').each(function() {
                    $(this).val('').trigger('change');
                });

                // Reinitialize DataTable
                table = initializeDataTable();

                // Show initial message
                showInitialMessage();

                // Clear session storage
                $.get("{{ route('procurement.pr.clear-search') }}")
                    .fail(function(xhr, status, error) {
                        console.error('Failed to clear search parameters:', error);
                    });
            });
        });
    </script>
@endsection
