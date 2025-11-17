<li class="nav-item dropdown">
    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        class="nav-link dropdown-toggle">Sync Data</a>
    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
        <li><a href="{{ route('master.sync-with-sap.index') }}" class="dropdown-item">Sync With SAP</a></li>
        @can('impor-sap-data')
            <li><a href="{{ route('master.dailypr.index') }}" class="dropdown-item">PR Import</a></li>
            <li><a href="{{ route('master.potemp.index') }}" class="dropdown-item">PO Import</a></li>
        @endcan
    </ul>
</li>
