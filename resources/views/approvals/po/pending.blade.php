@extends('layout.main')

@section('title_page')
    Pending Approvals
@endsection

@section('breadcrumb_title')
    <small>
        approvals / purchase order / pending
    </small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-aprv-po-links page="pending" />

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-1"></i>
                        Purchase Orders Requiring Your Approval
                    </h3>
                    <div class="card-tools">
                        <div class="btn-group" id="bulk-actions" style="display: none;">
                            <button type="button" class="btn btn-sm btn-success" id="bulk-approve-btn">
                                <i class="fas fa-check"></i> Approve Selected
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" id="bulk-reject-btn">
                                <i class="fas fa-times"></i> Reject Selected
                            </button>
                            <button type="button" class="btn btn-sm btn-info" id="bulk-export-btn">
                                <i class="fas fa-file-excel"></i> Export Selected
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="pending-table">
                        <thead>
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th>PO Number</th>
                                <th>Date</th>
                                <th>Supplier</th>
                                <th>Project</th>
                                <th>Approval Level</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Will be filled by AJAX -->
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
            var table = $('#pending-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                autoWidth: false,
                ajax: "{{ route('approvals.po.pending-data') }}",
                columns: [{
                        data: 'checkbox',
                        name: 'checkbox',
                        orderable: false,
                        searchable: false,
                        width: '30px'
                    },
                    {
                        data: 'doc_num',
                        name: 'doc_num',
                        searchable: true
                    },
                    {
                        data: 'doc_date',
                        name: 'doc_date',
                        searchable: false
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier_name',
                        searchable: false
                    },
                    {
                        data: 'project_code',
                        name: 'project_code',
                        searchable: true
                    },
                    {
                        data: 'approval_level',
                        name: 'approval_level',
                        searchable: false
                    },
                    {
                        data: 'total_amount',
                        name: 'total_amount',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [2, 'desc']
                ]
            });

            // Select all checkbox
            $('#select-all').on('change', function() {
                $('input[type="checkbox"][name="po_ids[]"]').prop('checked', this.checked);
                updateBulkActions();
            });

            // Individual checkbox change
            $(document).on('change', 'input[type="checkbox"][name="po_ids[]"]', function() {
                updateBulkActions();
                var allChecked = $('input[type="checkbox"][name="po_ids[]"]:checked').length === $('input[type="checkbox"][name="po_ids[]"]').length;
                $('#select-all').prop('checked', allChecked);
            });

            function updateBulkActions() {
                var selectedCount = $('input[type="checkbox"][name="po_ids[]"]:checked').length;
                if (selectedCount > 0) {
                    $('#bulk-actions').show();
                } else {
                    $('#bulk-actions').hide();
                }
            }

            // Bulk approve
            $('#bulk-approve-btn').on('click', function() {
                var selectedIds = getSelectedIds();
                if (selectedIds.length === 0) {
                    Swal.fire('Error', 'Please select at least one PO', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Approve Selected POs?',
                    text: `You are about to approve ${selectedIds.length} PO(s)`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Approve'
                }).then((result) => {
                    if (result.isConfirmed) {
                        bulkApprove(selectedIds);
                    }
                });
            });

            // Bulk reject
            $('#bulk-reject-btn').on('click', function() {
                var selectedIds = getSelectedIds();
                if (selectedIds.length === 0) {
                    Swal.fire('Error', 'Please select at least one PO', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Reject Selected POs?',
                    text: `You are about to reject ${selectedIds.length} PO(s)`,
                    icon: 'warning',
                    showCancelButton: true,
                    input: 'textarea',
                    inputPlaceholder: 'Enter rejection reason (optional)',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Reject'
                }).then((result) => {
                    if (result.isConfirmed) {
                        bulkReject(selectedIds, result.value || '');
                    }
                });
            });

            // Bulk export
            $('#bulk-export-btn').on('click', function() {
                var selectedIds = getSelectedIds();
                if (selectedIds.length === 0) {
                    Swal.fire('Error', 'Please select at least one PO', 'error');
                    return;
                }

                window.location.href = "{{ route('approvals.po.bulk-export') }}?ids=" + selectedIds.join(',');
            });

            function getSelectedIds() {
                var ids = [];
                $('input[type="checkbox"][name="po_ids[]"]:checked').each(function() {
                    ids.push($(this).val());
                });
                return ids;
            }

            function bulkApprove(ids) {
                $.ajax({
                    url: "{{ route('approvals.po.bulk-approve') }}",
                    method: 'POST',
                    data: {
                        ids: ids,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Success', response.message || 'POs approved successfully', 'success');
                        table.ajax.reload();
                        $('#select-all').prop('checked', false);
                        updateBulkActions();
                    },
                    error: function(xhr) {
                        var message = xhr.responseJSON?.message || 'Error approving POs';
                        Swal.fire('Error', message, 'error');
                    }
                });
            }

            function bulkReject(ids, notes) {
                $.ajax({
                    url: "{{ route('approvals.po.bulk-reject') }}",
                    method: 'POST',
                    data: {
                        ids: ids,
                        notes: notes,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Success', response.message || 'POs rejected successfully', 'success');
                        table.ajax.reload();
                        $('#select-all').prop('checked', false);
                        updateBulkActions();
                    },
                    error: function(xhr) {
                        var message = xhr.responseJSON?.message || 'Error rejecting POs';
                        Swal.fire('Error', message, 'error');
                    }
                });
            }
        });
    </script>
@endsection
