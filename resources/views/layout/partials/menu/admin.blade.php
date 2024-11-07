<li class="nav-item dropdown">
    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        class="nav-link dropdown-toggle">Admin</a>
    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">

        <li><a href="{{ route('admin.roles.index') }}" class="dropdown-item">Roles</a></li>
        {{-- @can('akses_permission') --}}
        <li><a href="{{ route('admin.permissions.index') }}" class="dropdown-item">Permissions</a></li>
        {{-- @endcan --}}
    </ul>
</li>
