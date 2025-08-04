@extends('layout.main')

@section('title', 'Search Item Prices')

@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.dashboard') }}">Consignment</a></li>
    <li class="breadcrumb-item active">Search</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Advanced Search</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('consignment.search') }}" method="GET">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="item_code">Item Code</label>
                                        <input type="text" class="form-control" id="item_code" name="item_code"
                                            value="{{ request('item_code') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="item_description">Description</label>
                                        <input type="text" class="form-control" id="item_description"
                                            name="item_description" value="{{ request('item_description') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="supplier_id">Supplier</label>
                                        <select class="form-control" id="supplier_id" name="supplier_id">
                                            <option value="">-- All Suppliers --</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="project">Project</label>
                                        <select class="form-control" id="project" name="project">
                                            <option value="">-- All Projects --</option>
                                            @foreach ($projects as $project)
                                                <option value="{{ $project->code }}"
                                                    {{ request('project') == $project->code ? 'selected' : '' }}>
                                                    {{ $project->code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="warehouse">Warehouse</label>
                                        <select class="form-control" id="warehouse" name="warehouse">
                                            <option value="">-- All Warehouses --</option>
                                            @foreach ($warehouses as $warehouse)
                                                <option value="{{ $warehouse->name }}"
                                                    {{ request('warehouse') == $warehouse->name ? 'selected' : '' }}>
                                                    {{ $warehouse->name }} ({{ $warehouse->code }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="part_number">Part Number</label>
                                        <input type="text" class="form-control" id="part_number" name="part_number"
                                            value="{{ request('part_number') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="brand">Brand</label>
                                        <input type="text" class="form-control" id="brand" name="brand"
                                            value="{{ request('brand') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                    <a href="{{ route('consignment.search') }}" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-redo"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Search Results</h3>
                    </div>
                    <div class="card-body">
                        <table id="item-prices-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th>Description</th>
                                    <th>Brand</th>
                                    <th>Part Number</th>
                                    <th>Supplier</th>
                                    <th>Warehouse</th>
                                    <th>UOM</th>
                                    <th>Price (IDR)</th>
                                    <th>Start Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($itemPrices as $item)
                                    <tr>
                                        <td>{{ $item->item_code ?? 'N/A' }}</td>
                                        <td>{{ $item->item_description ?? 'N/A' }}</td>
                                        <td>{{ $item->brand ?? 'N/A' }}</td>
                                        <td>{{ $item->part_number ?? 'N/A' }}</td>
                                        <td>{{ $item->supplier->name ?? 'N/A' }}</td>
                                        <td>{{ $item->warehouse ?? 'N/A' }}</td>
                                        <td>{{ $item->uom }}</td>
                                        <td>{{ number_format($item->price, 2) }}</td>
                                        <td data-order="{{ $item->start_date->format('Y-m-d') }}">
                                            {{ $item->start_date->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="{{ route('consignment.item-prices.show', $item->id) }}"
                                                class="btn btn-info btn-xs" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('consignment.history', $item->item_code) }}"
                                                class="btn btn-secondary btn-xs" title="View Price History">
                                                <i class="fas fa-history"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- DataTables & Plugins -->
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

    <script>
        $(function() {
            $("#item-prices-table").DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "pageLength": 25,
                "buttons": [
                    "copy", "csv", "excel", "pdf", "print", "colvis"
                ],
                "language": {
                    "search": "Filter results:",
                    "lengthMenu": "Show _MENU_ entries per page",
                    "zeroRecords": "No matching records found",
                    "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                    "infoEmpty": "Showing 0 to 0 of 0 entries",
                    "infoFiltered": "(filtered from _MAX_ total entries)"
                },
                "columnDefs": [{
                        "orderable": false,
                        "targets": 9
                    } // Disable sorting on actions column
                ],
                "order": [
                    [0, 'asc']
                ] // Default sort by item code
            }).buttons().container().appendTo('#item-prices-table_wrapper .col-md-6:eq(0)');

            // Apply the search from the form to the DataTable
            $('.card-body form').on('submit', function(e) {
                e.preventDefault();
                var table = $('#item-prices-table').DataTable();

                // Clear any existing filters
                table.search('').columns().search('').draw();

                // Apply filters from form inputs
                var itemCode = $('#item_code').val();
                var itemDesc = $('#item_description').val();
                var supplier = $('#supplier_id option:selected').text();
                var project = $('#project option:selected').text();
                var warehouse = $('#warehouse option:selected').text();
                var partNumber = $('#part_number').val();
                var brand = $('#brand').val();

                if (itemCode) table.column(0).search(itemCode);
                if (itemDesc) table.column(1).search(itemDesc);
                if (brand) table.column(2).search(brand);
                if (partNumber) table.column(3).search(partNumber);
                if (supplier && supplier !== '-- All Suppliers --') table.column(4).search(supplier);
                if (warehouse && warehouse !== '-- All Warehouses --') table.column(5).search(warehouse);

                table.draw();
            });

            // Reset button should clear the DataTable filters too
            $('a.btn-secondary').on('click', function() {
                var table = $('#item-prices-table').DataTable();
                table.search('').columns().search('').draw();
            });
        });
    </script>
@endsection
