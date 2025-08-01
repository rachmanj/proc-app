<a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-primary btn-xs">
    <i class="fas fa-edit"></i>
</a>
<form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline-block;"
    onsubmit="return confirm('Are you sure you want to delete this supplier?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-xs">
        <i class="fas fa-trash"></i>
    </button>
</form> 