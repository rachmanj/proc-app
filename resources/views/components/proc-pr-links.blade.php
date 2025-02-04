<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('procurement.pr.index', ['page' => 'dashboard']) }}"
                    class="btn {{ $page == 'dashboard' ? 'btn-secondary' : 'btn-light' }} btn-sm">
                    <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                </a>
                <a href="{{ route('procurement.pr.index', ['page' => 'search']) }}"
                    class="btn {{ $page == 'search' ? 'btn-secondary' : 'btn-light' }} btn-sm">
                    <i class="fas fa-search mr-1"></i> Search
                </a>
            </div>
        </div>
    </div>
</div>
