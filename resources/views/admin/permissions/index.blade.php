@extends('layout.main')

@section('title_page')
    Permissions
@endsection

@section('breadcrumb_title')
    permissions
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Permissions</div>
                    <button type="button" class="btn btn-sm btn-primary float-right" data-toggle="modal"
                        data-target="#createPermissionModal">
                        <i class="fas fa-plus"></i> Permission
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped" id="permissions">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Permission Name</th>
                                <th>Guard Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Permission Modal -->
    <div class="modal fade" id="createPermissionModal" tabindex="-1" role="dialog"
        aria-labelledby="createPermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPermissionModalLabel">Create Permission</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createPermissionForm">
                        @csrf
                        <div class="form-group">
                            <label for="permission_name">Permission Name</label>
                            <input type="text" class="form-control" id="permission_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="guard_name">Guard Name</label>
                            <input type="text" class="form-control" id="guard_name" name="guard_name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Create</button>
                    </form>
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
    <link rel="stylesheet" type="text/css" href="{{ asset('adminlte/plugins/datatables/css/datatables.min.css') }}" />
@endsection

@section('scripts')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables/datatables.min.js') }}"></script>

    <script>
        $(function() {
            $('#permissions').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.permissions.data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                    },
                    {
                        data: 'guard_name',
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                fixedHeader: true,
                // align coloumn content
                columnDefs: [{
                    targets: 0,
                    className: 'text-right'
                }, ]
            });

            $('#createPermissionForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('admin.permissions.store') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#createPermissionModal').modal('hide');
                        $('#permissions').DataTable().ajax.reload();
                        alert(response.success);
                    },
                    error: function(response) {
                        if (response.responseJSON && response.responseJSON.errors) {
                            let errors = response.responseJSON.errors;
                            let errorMessage = '';
                            for (let field in errors) {
                                errorMessage += errors[field].join(', ') + '\n';
                            }
                            alert(errorMessage);
                        } else {
                            alert('An error occurred while creating the permission');
                        }
                    }
                });
            });

            $('#editPermissionForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editPermissionModal').modal('hide');
                        $('#permissions').DataTable().ajax.reload();
                        alert('Permission updated successfully');
                    },
                    error: function(response) {
                        if (response.responseJSON && response.responseJSON.errors) {
                            let errors = response.responseJSON.errors;
                            let errorMessage = '';
                            for (let field in errors) {
                                errorMessage += errors[field].join(', ') + '\n';
                            }
                            alert(errorMessage);
                        } else {
                            alert('An error occurred while updating the permission');
                        }
                    }
                });
            });
        });

        function showEditModal(id, name, guardName) {
            $('#editPermissionForm').attr('action', '/admin/permissions/' + id);
            $('#edit_permission_name').val(name);
            $('#edit_guard_name').val(guardName);
            $('#editPermissionModal').modal('show');
        }

        function confirmDelete(form) {
            return confirm('Are you sure you want to delete this permission?');
        }
    </script>
@endsection
