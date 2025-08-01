
@extends('layout.main')

@section('title_page')
    Create PO Service
@endsection

@section('breadcrumb_title')
    po_service
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    @if (Session::has('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ Session::get('success') }}
                        </div>
                    @endif
                    @if (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ Session::get('error') }}
                        </div>
                    @endif
                    <h3 class="card-title">Add Items</h3>
                    <a href="{{ route('po_service.index') }}" class="btn btn-sm btn-primary float-right"><i
                            class="fas fa-undo"></i> Back</a>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Record ID / PO Service ID</dt>
                        <dd class="col-sm-8">: <b>{{ $po->id }}</b></dd>
                        <dt class="col-sm-4">PO No</dt>
                        <dd class="col-sm-8">: {{ $po->po_no }}</dd>
                        <dt class="col-sm-4">Date</dt>
                        <dd class="col-sm-8">: {{ $po->date ? date('d-M-Y', strtotime($po->date)) : '-' }}</dd>
                        <dt class="col-sm-4">Vendor</dt>
                        <dd class="col-sm-8">: {{ $vendor->name }}</dd>
                        <dt class="col-sm-4">Project</dt>
                        <dd class="col-sm-8">: {{ $po->project_code }}</dd>
                        <dt class="col-sm-4">Remarks</dt>
                        <dd class="col-sm-8">: {{ $po->remarks }}</dd>
                        <dt class="col-sm-4">Sub Total</dt>
                        <dd class="col-sm-8">: IDR <span id="sub-total">
                            @php
                                $subtotal = 0;
                                if($item_services) {
                                    foreach($item_services as $item) {
                                        $subtotal += ($item->qty * $item->unit_price);
                                    }
                                }
                            @endphp
                            {{ $item_services ? number_format($subtotal, 2) : '-' }}
                        </span></dd>
                        <dt class="col-sm-4">VAT</dt>
                        <dd class="col-sm-8">: IDR <span id="vat">{{ $po->is_vat == 1 ? number_format($subtotal * 0.11, 2) : '-' }}</span></dd>
                        <dt class="col-sm-4">Total Amount</dt>
                        <dd class="col-sm-8">: IDR <b id="total-amount">{{ $po->is_vat == 1 ? number_format($subtotal * 1.11, 2) : number_format($subtotal, 2) }}</b></dd>
 
                        <dt class="col-sm-4">Created by</dt>
                        <dd class="col-sm-8">: {{ $po->created_by }}</dd>
                    </dl>
                </div>
                <div class="card-header">

                    <button class="btn btn-sm btn-primary {{ $po->print_count > 2 ? 'disabled' : '' }}" data-toggle="modal"
                        data-target="#modal-input"><i class="fas fa-plus"></i> Item</button>
                    <button class="btn btn-sm btn-success {{ $po->print_count > 2 ? 'disabled' : '' }}" data-toggle="modal"
                        data-target="#modal-excel"><i class="fas fa-upload"></i> Upload Items</button>
                    <a href="{{ route('po_service.preview', $po->id) }}" class="btn btn-sm btn-info" target="_blank"><i
                            class="fas fa-print"></i> Preview</a>
                    <a href="{{ route('po_service.print_pdf', $po->id) }}"
                        class="btn btn-sm btn-warning {{ $po->print_count > 2 ? 'disabled' : '' }}" target="_blank"><i
                            class="fas fa-print"></i> Print ({{ $po->print_count }})</a>
                    <button type="button" id="btn-delete-all"
                        class="btn btn-sm btn-danger float-right {{ $po->print_count > 2 ? 'disabled' : '' }}"
                        data-id="{{ $po->id }}">
                        <i class="fas fa-trash"></i> Delete All Items
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered table-sm" id="table-items">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item Code</th>
                                <th>Item Desc</th>
                                <th>Qty</th>
                                <th>Uom</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal input manual --}}
    <div class="modal fade" id="modal-input">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Item</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('item_service.store', ['po_service' => $po->id]) }}" method="POST" id="form-item">
                    @csrf
                    <input type="hidden" name="po_service_id" value="{{ $po->id }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="item_code">Item Code <span class="text-danger">*</span></label>
                            <input type="text" name="item_code" id="item_code" maxlength="255"
                                class="form-control @error('item_code') is-invalid @enderror" required>
                            @error('item_code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="item_desc">Item Description <span class="text-danger">*</span></label>
                            <input type="text" name="item_desc" id="item_desc" maxlength="255"
                                class="form-control @error('item_desc') is-invalid @enderror" required>
                            @error('item_desc')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="qty">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="qty" id="qty" min="1" step="1"
                                class="form-control @error('qty') is-invalid @enderror" required>
                            @error('qty')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="uom">Unit of Measure <span class="text-danger">*</span></label>
                            <input type="text" name="uom" id="uom" maxlength="50"
                                class="form-control @error('uom') is-invalid @enderror" required>
                            @error('uom')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="unit_price">Unit Price <span class="text-danger">*</span></label>
                            <input type="number" name="unit_price" id="unit_price" min="0" step="0.01"
                                class="form-control @error('unit_price') is-invalid @enderror" required>
                            @error('unit_price')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="btn-save">
                            <i class="fas fa-save"></i> Save
                        </button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <!--Modal upload Excel -->
    <div class="modal fade" id="modal-excel">
        <div class="modal-dialog">
            <div class="modal-content animated rollIn">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-star"></i> Upload Items Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('item_service.import_item', $po->id) }}"
                        enctype="multipart/form-data" id="form-upload-items">
                        @csrf
                        <input type="hidden" name="po_service_id" value="{{ $po->id }}">
                        <label>Pilih file excel</label>
                        <div class="form-group">
                            <input type="file" name="file_upload" required="required" accept=".xls,.xlsx">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i
                                    class="fa fa-times"></i>
                                Close</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-save"></i>
                                Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal edit -->
    <div class="modal fade" id="modal-edit">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Edit Item</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form-edit">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_item_code">Item Code <span class="text-danger">*</span></label>
                            <input type="text" name="item_code" id="edit_item_code" maxlength="255"
                                class="form-control @error('item_code') is-invalid @enderror" required>
                            @error('item_code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="edit_item_desc">Item Description <span class="text-danger">*</span></label>
                            <input type="text" name="item_desc" id="edit_item_desc" maxlength="255"
                                class="form-control @error('item_desc') is-invalid @enderror" required>
                            @error('item_desc')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="edit_qty">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="qty" id="edit_qty" min="1" step="1"
                                class="form-control @error('qty') is-invalid @enderror" required>
                            @error('qty')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="edit_uom">Unit of Measure <span class="text-danger">*</span></label>
                            <input type="text" name="uom" id="edit_uom" maxlength="50"
                                class="form-control @error('uom') is-invalid @enderror" required>
                            @error('uom')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="edit_unit_price">Unit Price <span class="text-danger">*</span></label>
                            <input type="number" name="unit_price" id="edit_unit_price" min="0" step="0.01"
                                class="form-control @error('unit_price') is-invalid @enderror" required>
                            @error('unit_price')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="btn-update">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/toastr/toastr.min.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <!-- Toastr -->
    <script src="{{ asset('adminlte/plugins/toastr/toastr.min.js') }}"></script>

    <script>
        $(function() {
            // Initialize toastr
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "5000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // Function to initialize DataTable
            function initDataTable() {
                if ($.fn.DataTable.isDataTable('#table-items')) {
                    $('#table-items').DataTable().destroy();
                }

                return $('#table-items').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route("item_service.data") }}',
                        type: 'GET',
                        data: function(d) {
                            d.po_service_id = '{{ $po->id }}';
                        }
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'item_code', name: 'item_code' },
                        { data: 'item_desc', name: 'item_desc' },
                        { data: 'qty', name: 'qty', className: 'text-right' },
                        { data: 'uom', name: 'uom' },
                        { data: 'unit_price', name: 'unit_price', className: 'text-right' },
                        { data: 'item_amount', name: 'item_amount', className: 'text-right' },
                        { data: 'action', name: 'action', orderable: false, className: 'text-center', searchable: false }
                    ],
                    fixedHeader: true,
                    order: [[0, 'asc']],
                    language: {
                        processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>'
                    }
                });
            }

            // Initialize DataTable
            var table = initDataTable();

            // Form validation and submission
            $('#form-item').on('submit', function(e) {
                e.preventDefault();
                
                // Disable submit button
                $('#btn-save').prop('disabled', true);
                
                // Submit form via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // Close modal
                        $('#modal-input').modal('hide');
                        
                        // Reset form
                        $('#form-item')[0].reset();
                        
                        // Destroy and reinitialize DataTable
                        table.destroy();
                        table = initDataTable();
                        
                        // Show success message
                        toastr.success('Item has been added successfully');
                        updateSummary();
                    },
                    error: function(xhr) {
                        // Show error message
                        let errorMessage = 'Failed to add item';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                        
                        // Show validation errors if any
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                                toastr.error(xhr.responseJSON.errors[key][0]);
                            });
                        }
                    },
                    complete: function() {
                        // Re-enable submit button
                        $('#btn-save').prop('disabled', false);
                    }
                });
            });

            // Reset form when modal is closed
            $('#modal-input').on('hidden.bs.modal', function() {
                $('#form-item')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
            });

            // Handle Edit Button Click
            $(document).on('click', '.edit-item', function() {
                var id = $(this).data('id');
                
                // Disable update button
                $('#btn-update').prop('disabled', true);
                
                // Get item data
                $.get('{{ route("item_service.edit", "") }}/' + id, function(data) {
                    $('#edit_id').val(data.id);
                    $('#edit_item_code').val(data.item_code);
                    $('#edit_item_desc').val(data.item_desc);
                    $('#edit_qty').val(data.qty);
                    $('#edit_uom').val(data.uom);
                    $('#edit_unit_price').val(data.unit_price);
                    
                    // Enable update button
                    $('#btn-update').prop('disabled', false);
                });
            });

            // Handle Edit Form Submit
            $('#form-edit').on('submit', function(e) {
                e.preventDefault();
                
                var id = $('#edit_id').val();
                
                // Disable update button
                $('#btn-update').prop('disabled', true);
                
                // Submit form via AJAX
                $.ajax({
                    url: '{{ route("item_service.update", "") }}/' + id,
                    type: 'PUT',
                    data: $(this).serialize(),
                    success: function(response) {
                        // Close modal
                        $('#modal-edit').modal('hide');
                        
                        // Reset form
                        $('#form-edit')[0].reset();
                        
                        // Destroy and reinitialize DataTable
                        table.destroy();
                        table = initDataTable();
                        
                        // Show success message
                        toastr.success('Item has been updated successfully');
                        updateSummary();
                    },
                    error: function(xhr) {
                        // Show error message
                        let errorMessage = 'Failed to update item';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        toastr.error(errorMessage);
                        
                        // Show validation errors if any
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                                toastr.error(xhr.responseJSON.errors[key][0]);
                            });
                        }
                    },
                    complete: function() {
                        // Re-enable update button
                        $('#btn-update').prop('disabled', false);
                    }
                });
            });

            // Handle Delete Button Click
            $(document).on('click', '.delete-item', function() {
                var id = $(this).data('id');
                
                if (confirm('Are you sure you want to delete this item?')) {
                    $.ajax({
                        url: '{{ route("item_service.destroy", "") }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // Destroy and reinitialize DataTable
                            table.destroy();
                            table = initDataTable();
                            
                            // Show success message
                            toastr.success('Item has been deleted successfully');
                            updateSummary();
                        },
                        error: function(xhr) {
                            // Show error message
                            let errorMessage = 'Failed to delete item';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            toastr.error(errorMessage);
                        }
                    });
                }
            });

            // Reset edit form when modal is closed
            $('#modal-edit').on('hidden.bs.modal', function() {
                $('#form-edit')[0].reset();
                $('.is-invalid').removeClass('is-invalid');
            });

            // Handle Delete All Items Button Click
            $(document).on('click', '#btn-delete-all', function() {
                var id = $(this).data('id');
                
                if (confirm('Are you sure you want to delete all items in this PO?')) {
                    $.ajax({
                        url: '/po-service/' + id + '/delete-all',
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            // Destroy and reinitialize DataTable
                            table.destroy();
                            table = initDataTable();
                            
                            // Show success message
                            toastr.success('All items have been deleted successfully');
                            updateSummary();
                        },
                        error: function(xhr) {
                            // Show error message
                            let errorMessage = 'Failed to delete items';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            toastr.error(errorMessage);
                        }
                    });
                }
            });

            function updateSummary() {
                $.get('/po-service/{{ $po->id }}/summary', function(data) {
                    $('#sub-total').text(data.sub_total);
                    $('#vat').text(data.vat);
                    $('#total-amount').text(data.total);
                });
            }
        });
    </script>
@endsection
