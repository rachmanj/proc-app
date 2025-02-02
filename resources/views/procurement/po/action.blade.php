<div>
    <a href="{{ route('procurement.po.show', $model->id) }}" class="btn btn-xs btn-info me-1">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('procurement.po.edit', $model->id) }}" class="btn btn-xs btn-primary me-1">
        <i class="fas fa-edit"></i>
    </a>
    <button type="button" class="btn btn-xs btn-danger delete-po" data-id="{{ $model->id }}">
        <i class="fas fa-trash"></i>
    </button>
</div>
