@extends('layout.main')

@section('title_page')
    Sync With SAP
@endsection

@section('breadcrumb_title')
    <small>
        sync data / sync with sap
    </small>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-sync mr-2"></i>Sync Data with SAP B1</h4>
            </div>
            <div class="card-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="syncTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pr-tab" data-toggle="tab" href="#pr-panel" role="tab">
                            <i class="fas fa-file-alt mr-2"></i>PR Sync
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="po-tab" data-toggle="tab" href="#po-panel" role="tab">
                            <i class="fas fa-shopping-cart mr-2"></i>PO Sync
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="syncTabContent">
                    <!-- PR Panel -->
                    <div class="tab-pane fade show active" id="pr-panel" role="tabpanel">
                        @include('master.sync-with-sap.partials.pr-panel', ['lastSync' => $lastPrSync])
                    </div>

                    <!-- PO Panel -->
                    <div class="tab-pane fade" id="po-panel" role="tabpanel">
                        @include('master.sync-with-sap.partials.po-panel', ['lastSync' => $lastPoSync])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
@endsection
