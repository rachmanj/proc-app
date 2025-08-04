@can('access_consignment')
    <li class="nav-item dropdown">
        <a id="dropdownConsignment" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
            class="nav-link dropdown-toggle">Consignment</a>
        <ul aria-labelledby="dropdownConsignment" class="dropdown-menu border-0 shadow">
            <li><a href="{{ route('consignment.dashboard') }}" class="dropdown-item">Dashboard</a></li>
            <li><a href="{{ route('consignment.item-prices.index') }}" class="dropdown-item">Item Prices</a></li>
            <li><a href="{{ route('consignment.warehouses.index') }}" class="dropdown-item">Warehouses</a></li>
            @can('search_consignment')
                <li><a href="{{ route('consignment.search') }}" class="dropdown-item">Search</a></li>
            @endcan
            @can('upload_consignment')
                <li><a href="{{ route('consignment.imports.upload') }}" class="dropdown-item">Import</a></li>
            @endcan
        </ul>
    </li>
@endcan
