<li class="nav-item dropdown">
    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        class="nav-link dropdown-toggle">Approvals</a>
    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
        @can('akses_approval')
            <li><a href="{{ route('approvals.po.index') }}" class="dropdown-item">Purchase Order</a></li>
        @endcan
    </ul>
</li>
