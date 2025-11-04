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
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Quick Filters -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="mb-2"><strong>Quick Filters:</strong></label>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary quick-filter" data-filter="my-prs">
                                    <i class="fas fa-user"></i> My PRs
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning quick-filter" data-filter="pending">
                                    <i class="fas fa-clock"></i> Pending Approval
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger quick-filter" data-filter="overdue">
                                    <i class="fas fa-exclamation-triangle"></i> Overdue (>30 days)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info quick-filter" data-filter="this-month">
                                    <i class="fas fa-calendar"></i> This Month
                                </button>
                            </div>
                        </div>
                    </div>

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
                                    <label>Status <small>(Multiple selection)</small></label>
                                    <select class="form-control select2" id="pr_status" name="pr_status[]" multiple="multiple" data-placeholder="Select Status">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}"
                                                {{ in_array($status, (array)($searchParams['pr_status'] ?? [])) ? 'selected' : '' }}>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date Range</label>
                                    <input type="text" class="form-control" id="date_range" name="date_range" placeholder="Select date range">
                                    <input type="hidden" id="date_from" name="date_from">
                                    <input type="hidden" id="date_to" name="date_to">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <button type="reset" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </button>
                                <button type="button" class="btn btn-sm btn-info" id="save-filter-btn">
                                    <i class="fas fa-save"></i> Save Filter
                                </button>
                                <button type="button" class="btn btn-sm btn-success" id="load-filter-btn">
                                    <i class="fas fa-folder-open"></i> Load Saved Filter
                                </button>
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
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    <!-- Date Range Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
@endsection

@section('scripts')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
    <!-- Date Range Picker -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Initialize Date Range Picker
            $('#date_range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });

            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' / ' + picker.endDate.format('YYYY-MM-DD'));
                $('#date_from').val(picker.startDate.format('YYYY-MM-DD'));
                $('#date_to').val(picker.endDate.format('YYYY-MM-DD'));
            });

            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                $('#date_from').val('');
                $('#date_to').val('');
            });

            // Quick Filters
            $('.quick-filter').on('click', function() {
                const filter = $(this).data('filter');
                
                // Reset all filters first
                $('#search-form')[0].reset();
                $('.select2').val(null).trigger('change');
                
                switch(filter) {
                    case 'my-prs':
                        // Filter by current user - this would need backend support
                        break;
                    case 'pending':
                        $('#pr_status').val(['OPEN', 'progress']).trigger('change');
                        break;
                    case 'overdue':
                        // Set date range to show PRs older than 30 days
                        const thirtyDaysAgo = moment().subtract(30, 'days');
                        $('#date_range').data('daterangepicker').setStartDate(thirtyDaysAgo);
                        $('#date_range').data('daterangepicker').setEndDate(moment());
                        $('#date_range').val(thirtyDaysAgo.format('YYYY-MM-DD') + ' / ' + moment().format('YYYY-MM-DD'));
                        $('#date_from').val(thirtyDaysAgo.format('YYYY-MM-DD'));
                        $('#date_to').val(moment().format('YYYY-MM-DD'));
                        break;
                    case 'this-month':
                        const startOfMonth = moment().startOf('month');
                        const endOfMonth = moment().endOf('month');
                        $('#date_range').data('daterangepicker').setStartDate(startOfMonth);
                        $('#date_range').data('daterangepicker').setEndDate(endOfMonth);
                        $('#date_range').val(startOfMonth.format('YYYY-MM-DD') + ' / ' + endOfMonth.format('YYYY-MM-DD'));
                        $('#date_from').val(startOfMonth.format('YYYY-MM-DD'));
                        $('#date_to').val(endOfMonth.format('YYYY-MM-DD'));
                        break;
                }
                
                // Trigger search
                if (table) {
                    table.draw();
                }
            });

            // Save Filter Preset
            $('#save-filter-btn').on('click', function() {
                const filterName = prompt('Enter a name for this filter preset:');
                if (filterName) {
                    const filterData = {
                        pr_no: $('#pr_no').val(),
                        pr_draft_no: $('#pr_draft_no').val(),
                        pr_rev_no: $('#pr_rev_no').val(),
                        priority: $('#priority').val(),
                        pr_status: $('#pr_status').val(),
                        pr_type: $('#pr_type').val(),
                        project_code: $('#project_code').val(),
                        for_unit: $('#for_unit').val(),
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val()
                    };
                    
                    const savedFilters = JSON.parse(localStorage.getItem('pr_filter_presets') || '{}');
                    savedFilters[filterName] = filterData;
                    localStorage.setItem('pr_filter_presets', JSON.stringify(savedFilters));
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Filter Saved',
                        text: 'Filter preset "' + filterName + '" has been saved successfully!',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });

            // Load Filter Preset
            $('#load-filter-btn').on('click', function() {
                const savedFilters = JSON.parse(localStorage.getItem('pr_filter_presets') || '{}');
                const filterNames = Object.keys(savedFilters);
                
                if (filterNames.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Saved Filters',
                        text: 'You don\'t have any saved filter presets yet.'
                    });
                    return;
                }
                
                // Create options HTML
                let optionsHtml = '<select class="form-control" id="preset-select">';
                filterNames.forEach(function(name) {
                    optionsHtml += '<option value="' + name + '">' + name + '</option>';
                });
                optionsHtml += '</select>';
                
                Swal.fire({
                    title: 'Load Filter Preset',
                    html: optionsHtml,
                    showCancelButton: true,
                    confirmButtonText: 'Load',
                    cancelButtonText: 'Cancel',
                    didOpen: function() {
                        $('#preset-select').focus();
                    },
                    preConfirm: function() {
                        const selectedName = $('#preset-select').val();
                        return selectedName;
                    }
                }).then(function(result) {
                    if (result.isConfirmed && result.value) {
                        const filterData = savedFilters[result.value];
                        
                        // Apply filter data
                        $('#pr_no').val(filterData.pr_no || '');
                        $('#pr_draft_no').val(filterData.pr_draft_no || '');
                        $('#pr_rev_no').val(filterData.pr_rev_no || '');
                        $('#priority').val(filterData.priority || '').trigger('change');
                        $('#pr_status').val(filterData.pr_status || []).trigger('change');
                        $('#pr_type').val(filterData.pr_type || '').trigger('change');
                        $('#project_code').val(filterData.project_code || '').trigger('change');
                        $('#for_unit').val(filterData.for_unit || '').trigger('change');
                        
                        if (filterData.date_from && filterData.date_to) {
                            $('#date_range').data('daterangepicker').setStartDate(moment(filterData.date_from));
                            $('#date_range').data('daterangepicker').setEndDate(moment(filterData.date_to));
                            $('#date_range').val(filterData.date_from + ' / ' + filterData.date_to);
                            $('#date_from').val(filterData.date_from);
                            $('#date_to').val(filterData.date_to);
                        }
                        
                        // Trigger search
                        if (table) {
                            table.draw();
                        }
                    }
                });
            });

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
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            className: 'btn btn-sm btn-success',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            className: 'btn btn-sm btn-danger',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'csvHtml5',
                            text: '<i class="fas fa-file-csv"></i> CSV',
                            className: 'btn btn-sm btn-info',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print"></i> Print',
                            className: 'btn btn-sm btn-secondary',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'colvis',
                            text: '<i class="fas fa-eye"></i> Columns',
                            className: 'btn btn-sm btn-primary'
                        }
                    ],
                    ajax: {
                        url: '{{ route('procurement.pr.search') }}',
                        data: function(d) {
                            d.pr_no = $('#pr_no').val();
                            d.pr_draft_no = $('#pr_draft_no').val();
                            d.pr_rev_no = $('#pr_rev_no').val();
                            d.priority = $('#priority').val();
                            d.pr_status = $('#pr_status').val(); // Array for multi-select
                            d.pr_type = $('#pr_type').val();
                            d.project_code = $('#project_code').val();
                            d.for_unit = $('#for_unit').val();
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
                        emptyTable: "Please click search to view data",
                        processing: "Processing...",
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        infoEmpty: "Showing 0 to 0 of 0 entries",
                        infoFiltered: "(filtered from _MAX_ total entries)",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    },
                    initComplete: function(settings, json) {
                        // Append buttons container
                        const api = this.api();
                        api.buttons().container().appendTo('#search-results-table_wrapper .col-md-6:eq(0)');
                        
                        // Load saved column visibility preferences
                        const savedCols = localStorage.getItem('pr_table_cols');
                        if (savedCols) {
                            try {
                                const hiddenCols = JSON.parse(savedCols);
                                hiddenCols.forEach(function(colIndex) {
                                    api.column(colIndex).visible(false);
                                });
                            } catch(e) {
                                console.error('Error loading column preferences:', e);
                            }
                        }
                    }
                });
            }
            
            // Save column visibility preferences when table is initialized
            $(document).on('init.dt', '#search-results-table', function() {
                const table = $(this).DataTable();
                table.on('column-visibility', function(e, settings, column, state) {
                    const hiddenCols = [];
                    table.columns().every(function() {
                        if (!this.visible()) {
                            hiddenCols.push(this.index());
                        }
                    });
                    localStorage.setItem('pr_table_cols', JSON.stringify(hiddenCols));
                });
            });

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
