<!DOCTYPE html>

<html lang="en">
@include('layout.partials.head')

<body class="hold-transition layout-top-nav layout-navbar-fixed">
    <div class="wrapper">

        @include('layout.partials.navbar')
        @include('sweetalert::alert')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('title_page')</small></h1>
                        </div><!-- /.col -->
                        @include('layout.partials.breadcrumb')
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <div class="content">
                <div class="container">

                    @yield('content')

                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        @include('layout.partials.footer')

    </div>
    <!-- ./wrapper -->

    <!-- REQUIRED SCRIPTS -->
    @include('layout.partials.script')

</body>

</html>
