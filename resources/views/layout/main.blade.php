<!DOCTYPE html>

<html lang="en">
@include('layout.partials.head')

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Include SweetAlert2 globally --}}
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.css') }}">
</head>

<body class="hold-transition layout-top-nav layout-navbar-fixed">
    <div class="wrapper">

        @include('layout.partials.navbar')
        @include('layout.partials.loading')
        @include('sweetalert::alert')

        {{-- Flash Messages --}}
        @if(session('success') || session('error') || session('warning') || session('info'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    @if(session('success'))
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: '{{ session('success') }}',
                            position: 'center',
                            showConfirmButton: true,
                            timer: 5000
                        });
                    @endif

                    @if(session('error'))
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            html: '{{ session('error') }}',
                            position: 'center',
                            showConfirmButton: true,
                            confirmButtonColor: '#dc3545',
                            allowOutsideClick: false
                        });
                    @endif

                    @if(session('warning'))
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: '{{ session('warning') }}',
                            position: 'center',
                            showConfirmButton: true
                        });
                    @endif

                    @if(session('info'))
                        Swal.fire({
                            icon: 'info',
                            title: 'Info!',
                            text: '{{ session('info') }}',
                            position: 'center',
                            showConfirmButton: true
                        });
                    @endif
                });
            </script>
        @endif

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

    @yield('scripts')

</body>

</html>
