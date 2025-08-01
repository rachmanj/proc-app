<div class="d-flex justify-content-center gap-2">
    <a href="{{ route('procurement.po.show', $model->id) }}" 
        class="btn btn-xs btn-info me-1"
        data-toggle="tooltip"
        data-placement="top"
        title="View Purchase Order">
        <i class="fas fa-eye"></i>
    </a>
    &nbsp; &nbsp;
    <a href="{{ route('procurement.po.edit', $model->id) }}" 
        class="btn btn-xs btn-primary me-1"
        data-toggle="tooltip"
        data-placement="top"
        title="Edit Purchase Order">
        <i class="fas fa-edit"></i>
    </a>
    <!-- <button type="button" 
        class="btn btn-xs btn-danger delete-po" 
        data-id="{{ $model->id }}"
        data-toggle="tooltip"
        data-placement="top"
        title="Delete Purchase Order">
        <i class="fas fa-trash"></i>
    </button> -->
</div>

@push('styles')
<style>
    .custom-tooltip {
        --bs-tooltip-bg: #2c3e50;
        --bs-tooltip-color: #fff;
        --bs-tooltip-opacity: 1;
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.375rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .custom-tooltip .tooltip-inner {
        max-width: 200px;
        padding: 0.5rem 0.75rem;
        text-align: left;
    }
</style>
@endpush

@push('scripts')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
