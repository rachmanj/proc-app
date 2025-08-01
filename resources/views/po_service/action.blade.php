<div class="btn-group">
    <a href="{{ route('po_service.show', $row->id) }}" class="btn btn-info btn-sm">
        <i class="fas fa-eye"></i>
    </a>
    &nbsp;&nbsp;
    <a href="{{ route('po_service.edit', $row->id) }}" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i>
    </a>
    &nbsp;&nbsp;
    <a href="{{ route('po_service.add_items', $row->id) }}" class="btn btn-sm btn-success">
        <i class="fas fa-plus"></i>
    </a>
    &nbsp;&nbsp;
    <a href="{{ route('po_service.preview', $row->id) }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-print"></i>
    </a>
    &nbsp;&nbsp;
    <form action="{{ route('po_service.destroy', $row->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div> 