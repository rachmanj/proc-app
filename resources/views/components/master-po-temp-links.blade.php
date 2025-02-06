<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('master.po.index', ['page' => 'dashboard']) }}"
                    class="btn {{ $page == 'dashboard' ? 'btn-secondary' : 'btn-light' }} btn-sm">
                    <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                </a>
                <a href="{{ route('master.po.index', ['page' => 'list']) }}"
                    class="btn {{ $page == 'list' ? 'btn-secondary' : 'btn-light' }} btn-sm">
                    <i class="fas fa-list mr-1"></i> List
                </a>
            </div>
        </div>
    </div>
</div>
