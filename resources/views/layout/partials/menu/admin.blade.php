<li class="nav-item dropdown">
    <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        class="nav-link dropdown-toggle">Admin</a>
    <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
        @can('akses_user')
            <li><a href="{{ route('admin.users.index') }}" class="dropdown-item">User Admin</a></li>
        @endcan
        @can('akses_permission')
            <li><a href="{{ route('admin.roles.index') }}" class="dropdown-item">Roles</a></li>
            <li><a href="{{ route('admin.permissions.index') }}" class="dropdown-item">Permissions</a></li>
        @endcan
    </ul>
</li>
