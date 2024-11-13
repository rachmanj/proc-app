@if ($model->is_active == 1)
    <form action="{{ route('admin.users.deactivate', $model->id) }}" method="POST" class="d-inline">
        @csrf @method('PUT')
        <button onclick="return confirm('Are you sure?')" type="submit" class="btn btn-xs btn-warning">deactivate</button>
    </form>
@endif

@if ($model->is_active == 0)
    <form action="{{ route('admin.users.activate', $model->id) }}" method="POST" class="d-inline">
        @csrf @method('PUT')
        <button onclick="return confirm('Are you sure?')" type="submit" class="btn btn-xs btn-warning">activate</button>
    </form>
@endif

<a href="{{ route('admin.users.edit', $model->id) }}" class="btn btn-xs btn-info d-inline">edit</a>

@if ($model->is_active == 0)
    <form action="{{ route('admin.users.destroy', $model->id) }}" method="POST" class="d-inline">
        @csrf @method('DELETE')
        <button class="btn btn-xs btn-danger" type="submit"
            onclick="return confirm('Are You sure You want to delete this user?')">delete</button>
    </form>
@endif
