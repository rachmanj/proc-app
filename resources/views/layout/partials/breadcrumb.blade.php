<div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}"><i class="fas fa-home"></i> Home</a>
        </li>
        @hasSection('breadcrumb')
            @yield('breadcrumb')
        @else
            @hasSection('breadcrumb_title')
                <li class="breadcrumb-item active">@yield('breadcrumb_title')</li>
            @endif
        @endif
    </ol>
</div><!-- /.col -->
