<div class="btn-group">
    <button type="button" class="btn btn-xs btn-warning edit-item" data-id="{{ $item->id }}" data-toggle="modal" data-target="#modal-edit">
        <i class="fas fa-edit"></i>
    </button>
    &nbsp;&nbsp;
    <button type="button" class="btn btn-xs btn-danger delete-item" data-id="{{ $item->id }}">
        <i class="fas fa-trash"></i>
    </button>
</div>
