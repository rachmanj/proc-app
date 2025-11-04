<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PROC App</title>

    <!-- Google Font: Source Sans Pro -->
    {{-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> --}}
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <h2><b>PROC</b> App<small> | v.2.1</small></h2>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">

                @if (session()->has('success'))
                    <div class="alert alert-success alert-dismissible">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    </div>
                @endif

                @if (session()->has('loginError'))
                    <div class="alert alert-danger alert-dismissible">
                        {{ session('loginError') }}
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    </div>
                @endif

                <p class="login-box-msg">Sign in to start your session</p>

                <form action="#" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="text" name="username"
                            class="form-control @error('username') is-invalid @enderror" placeholder="Username"
                            autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                </form>

                <p class="mb-0">
                    <a href="{{ route('register') }}" class="text-center">Register new account</a>
                </p>
            </div>
            <!-- /.login-card-body -->
        </div>

        <!-- What's New Section -->
        <div class="card mt-3" id="whatsNewCard">
            <div class="card-header p-2" style="cursor: pointer;" data-toggle="collapse" data-target="#whatsNewContent" aria-expanded="false">
                <h5 class="card-title mb-0">
                    <i class="fas fa-star text-warning"></i> 
                    <strong>What's New</strong>
                    <small class="text-muted">- Recent Improvements</small>
                    <span class="float-right">
                        <i class="fas fa-chevron-down" id="whatsNewIcon"></i>
                    </span>
                </h5>
            </div>
            <div id="whatsNewContent" class="collapse">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-chart-line text-primary mr-2 mt-1"></i>
                                <div>
                                    <strong>Enhanced Dashboard</strong>
                                    <small class="d-block text-muted">Real-time metrics, charts, and quick actions</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-file-excel text-success mr-2 mt-1"></i>
                                <div>
                                    <strong>Reporting & Analytics</strong>
                                    <small class="d-block text-muted">Comprehensive reports with export capabilities</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-tasks text-info mr-2 mt-1"></i>
                                <div>
                                    <strong>Bulk Operations</strong>
                                    <small class="d-block text-muted">Bulk approval, rejection, and export</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-search text-warning mr-2 mt-1"></i>
                                <div>
                                    <strong>Advanced Search</strong>
                                    <small class="d-block text-muted">Date filters, multi-select, saved presets</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-bell text-danger mr-2 mt-1"></i>
                                <div>
                                    <strong>Notification System</strong>
                                    <small class="d-block text-muted">Real-time notifications and alerts</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-magic text-purple mr-2 mt-1"></i>
                                <div>
                                    <strong>UX Improvements</strong>
                                    <small class="d-block text-muted">Loading indicators, tooltips, breadcrumbs</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="dismissWhatsNew">
                            <i class="fas fa-times"></i> Dismiss
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Check if user has dismissed the "What's New" section
            const dismissed = localStorage.getItem('whatsNewDismissed');
            if (dismissed === 'true') {
                $('#whatsNewCard').hide();
            } else {
                // Auto-expand on first visit (optional)
                // $('#whatsNewContent').collapse('show');
            }

            // Handle collapse icon rotation
            $('#whatsNewContent').on('show.bs.collapse', function() {
                $('#whatsNewIcon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });

            $('#whatsNewContent').on('hide.bs.collapse', function() {
                $('#whatsNewIcon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            });

            // Dismiss button functionality
            $('#dismissWhatsNew').on('click', function() {
                localStorage.setItem('whatsNewDismissed', 'true');
                $('#whatsNewCard').fadeOut(300);
            });
        });
    </script>

    <style>
        .text-purple {
            color: #6f42c1 !important;
        }

        #whatsNewCard {
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        #whatsNewCard .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        #whatsNewCard .card-header:hover {
            background-color: #e9ecef;
        }

        #whatsNewCard .card-body {
            background-color: #ffffff;
        }

        #whatsNewCard .fa-star {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        .login-box {
            width: 360px;
        }

        @media (max-width: 576px) {
            .login-box {
                width: 90%;
                margin-top: 20px;
            }
        }
    </style>
</body>

</html>
