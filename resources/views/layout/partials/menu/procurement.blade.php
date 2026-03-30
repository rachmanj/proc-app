<li class="nav-item dropdown">
    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        class="nav-link dropdown-toggle">Procurement</a>
    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
    @can('akses_proc_pr')
            <li><a href="{{ route('procurement.pr.index') }}" class="dropdown-item">Purchase Requisition</a></li>
        @endcan
        @can('akses_proc_po')
            <li>
                <a href="{{ route('procurement.po.index') }}" class="dropdown-item">
                    Purchase Order
                    @php
                        $revisionCount = \App\Models\PurchaseOrder::where('status', 'revision')->count();
                    @endphp
                    @if($revisionCount > 0)
                        <span class="badge bg-danger float-end">{{ $revisionCount }}</span>
                    @endif
                </a>
            </li>
        @endcan
        <li class="dropdown-divider"></li>
        <li>
            <a href="{{ route('procurement.collaboration.watchlist') }}" class="dropdown-item">
                <i class="fas fa-star mr-2"></i> My Watchlist
                @php
                    $followCount = \App\Models\PrFollow::where('user_id', auth()->id())->count() + 
                                   \App\Models\PoFollow::where('user_id', auth()->id())->count();
                @endphp
                @if($followCount > 0)
                    <span class="badge bg-primary float-end">{{ $followCount }}</span>
                @endif
            </a>
        </li>
    </ul>
</li>
