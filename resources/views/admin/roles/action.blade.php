<a href="{{ route('admin.roles.edit', $model->id) }}" class="btn btn-xs btn-warning">edit</a>

<form action="{{ route('admin.roles.destroy', $model->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-xs btn-danger"
        onclick="return confirm('Are you sure you want to delete this role?')">delete</button>
</form>
