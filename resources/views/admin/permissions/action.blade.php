<button type="button" class="btn btn-primary btn-xs" data-toggle="modal"
    data-target="#editPermissionModal-{{ $permission->id }}">
    Edit
</button>
<form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" style="display:inline-block;"
    onsubmit="return confirmDelete(this);">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-xs">Delete</button>
</form>

<!-- Edit Permission Modal -->
<div class="modal fade" id="editPermissionModal-{{ $permission->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editPermissionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPermissionModalLabel">Edit Permission</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.permissions.update', $permission->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="edit_permission_name">Permission Name</label>
                        <input type="text" class="form-control" name="name"
                            value="{{ old('name', $permission->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_guard_name">Guard Name</label>
                        <input type="text" class="form-control" name="guard_name"
                            value="{{ old('guard_name', $permission->guard_name) }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(form) {
        return confirm('Are you sure you want to delete this permission?');
    }
</script>
