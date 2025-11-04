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
                                <button type="button" class="btn btn-sm btn-outline-primary quick-filter" data-filter="my-pos">
                                    <i class="fas fa-user"></i> My POs
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning quick-filter" data-filter="pending">
                                    <i class="fas fa-clock"></i> Pending Approval
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success quick-filter" data-filter="approved">
                                    <i class="fas fa-check"></i> Approved
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info quick-filter" data-filter="this-month">
                                    <i class="fas fa-calendar"></i> This Month
                                </button>
                            </div>
                        </div>
                    </div>

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
                                <label for="status">Status <small>(Multiple selection)</small></label>
                                <select class="form-control select2" id="status" name="status[]" multiple="multiple" data-placeholder="Select Status">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="date_range">Date Range</label>
                                <input type="text" class="form-control" id="date_range" name="date_range" placeholder="Select date range">
                                <input type="hidden" id="date_from" name="date_from">
                                <input type="hidden" id="date_to" name="date_to">
                            </div>
                        </div>

                        <div class="row">
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
        $(document).ready(function() {
            // Initialize Select2 with Bootstrap 4 theme
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
                    case 'my-pos':
                        // Filter by current user - this would need backend support
                        break;
                    case 'pending':
                        $('#status').val(['submitted']).trigger('change');
                        break;
                    case 'approved':
                        $('#status').val(['approved']).trigger('change');
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
                        doc_num: $('#doc_num').val(),
                        pr_num: $('#pr_num').val(),
                        supplier_name: $('#supplier_name').val(),
                        unit_no: $('#unit_no').val(),
                        project_code: $('#project_code').val(),
                        status: $('#status').val(),
                        date_from: $('#date_from').val(),
                        date_to: $('#date_to').val()
                    };
                    
                    const savedFilters = JSON.parse(localStorage.getItem('po_filter_presets') || '{}');
                    savedFilters[filterName] = filterData;
                    localStorage.setItem('po_filter_presets', JSON.stringify(savedFilters));
                    
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
                const savedFilters = JSON.parse(localStorage.getItem('po_filter_presets') || '{}');
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
                        $('#doc_num').val(filterData.doc_num || '');
                        $('#pr_num').val(filterData.pr_num || '');
                        $('#supplier_name').val(filterData.supplier_name || '').trigger('change');
                        $('#unit_no').val(filterData.unit_no || '').trigger('change');
                        $('#project_code').val(filterData.project_code || '').trigger('change');
                        $('#status').val(filterData.status || []).trigger('change');
                        
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

            // Initialize DataTable
            let table = $('#po-table').DataTable({
                processing: true,
                serverSide: true,
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
                    url: '{{ route('procurement.po.search') }}',
                    data: function(d) {
                        d.doc_num = $('#doc_num').val();
                        d.pr_num = $('#pr_num').val();
                        d.supplier_name = $('#supplier_name').val();
                        d.unit_no = $('#unit_no').val();
                        d.project_code = $('#project_code').val();
                        d.status = $('#status').val(); // Array for multi-select
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
                ],
                language: {
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
                initComplete: function() {
                    // Append buttons container
                    table.buttons().container().appendTo('#po-table_wrapper .col-md-6:eq(0)');
                    
                    // Load saved column visibility preferences
                    const savedCols = localStorage.getItem('po_table_cols');
                    if (savedCols) {
                        try {
                            const hiddenCols = JSON.parse(savedCols);
                            hiddenCols.forEach(function(colIndex) {
                                table.column(colIndex).visible(false);
                            });
                        } catch(e) {
                            console.error('Error loading column preferences:', e);
                        }
                    }
                }
            });

            // Save column visibility preferences
            table.on('column-visibility', function(e, settings, column, state) {
                const hiddenCols = [];
                table.columns().every(function() {
                    if (!this.visible()) {
                        hiddenCols.push(this.index());
                    }
                });
                localStorage.setItem('po_table_cols', JSON.stringify(hiddenCols));
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
