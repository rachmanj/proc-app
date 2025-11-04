<!-- Navbar -->
<nav class="main-header navbar navbar-expand-md navbar-light navbar-dark layout-fixed">
    <div class="container">
        <a href="/"class="navbar-brand">
            <img src="{{ asset('adminlte/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
                class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text text-white font-weight-light"><strong>PROC</strong> App</span>
        </a>

        <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse"
            aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse order-3" id="navbarCollapse">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                </li>

                {{-- Reports Menu --}}
                @include('layout.partials.menu.reports')

                {{-- <a href="#" class="nav-link">Search</a> --}}

                @can('view_poservice')
                    @include('layout.partials.menu.po_service')
                @endcan

                @can('akses_procurement')
                    @include('layout.partials.menu.procurement')
                @endcan

                @can('akses_approval')
                    @include('layout.partials.menu.approvals')
                @endcan

                @can('access_consignment')
                    @include('layout.partials.menu.consignment')
                @endcan

                @can('akses_master')
                    @include('layout.partials.menu.master')
                @endcan

                @can('akses_admin')
                    @include('layout.partials.menu.admin')
                @endcan

            </ul>
        </div>

        <!-- Right navbar links -->
        <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
            <!-- Notifications Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" id="notificationDropdown">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge" id="notificationBadge">0</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" id="notificationMenu">
                    <span class="dropdown-item dropdown-header" id="notificationHeader">0 Notifications</span>
                    <div class="dropdown-divider"></div>
                    <div id="notificationList">
                        <div class="dropdown-item text-center">
                            <span>Loading notifications...</span>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer" id="markAllReadBtn">Mark All as Read</a>
                </div>
            </li>

            <li class="nav-item dropdown">
                <a id="dropdownPayreq" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    class="nav-link dropdown-toggle">{{ auth()->user()->name }} ({{ auth()->user()->project }})</a>
                <ul aria-labelledby="dropdownPayreq" class="dropdown-menu border-0 shadow">
                    <li>
                        <a href="{{ route('admin.users.change_password', auth()->user()->id) }}"
                            class="dropdown-item">Change
                            Password</a>
                    </li>
                    <li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" class="dropdown-item"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
<!-- /.navbar -->
