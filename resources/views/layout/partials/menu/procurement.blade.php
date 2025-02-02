<li class="nav-item dropdown">
    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        class="nav-link dropdown-toggle">Procurement</a>
    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
        @can('akses_proc_po')
            <li><a href="{{ route('procurement.po.index') }}" class="dropdown-item">Purchase Order</a></li>
        @endcan
    </ul>
</li>
