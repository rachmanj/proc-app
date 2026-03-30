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

                <hr class="my-4">

                <h5 class="mb-3"><i class="fas fa-history mr-2"></i>Recent SAP sync activity</h5>
                <p class="text-muted small mb-2">Last 10 sync runs (newest first). Times use the application timezone ({{ config('app.timezone') }}).</p>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Synced at</th>
                                <th>Type</th>
                                <th>Date range (query)</th>
                                <th class="text-right">Synced</th>
                                <th class="text-right">Created</th>
                                <th class="text-right">Skipped</th>
                                <th>Result</th>
                                <th>By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSyncLogs as $log)
                                <tr>
                                    <td class="text-nowrap">{{ $log->created_at->timezone(config('app.timezone'))->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        @if($log->data_type === 'PR')
                                            <span class="badge badge-info">PR</span>
                                        @else
                                            <span class="badge badge-primary">PO</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap">{{ $log->start_date?->format('Y-m-d') }} → {{ $log->end_date?->format('Y-m-d') }}</td>
                                    <td class="text-right">{{ number_format($log->records_synced ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($log->records_created ?? 0) }}</td>
                                    <td class="text-right">{{ number_format($log->records_skipped ?? 0) }}</td>
                                    <td class="small">
                                        <span class="text-muted">SAP:</span>
                                        <span class="badge badge-{{ $log->sync_status === 'success' ? 'success' : ($log->sync_status === 'partial' ? 'warning' : 'danger') }}">{{ $log->sync_status }}</span>
                                        @if($log->convert_status)
                                            <br>
                                            <span class="text-muted">Import:</span>
                                            <span class="badge badge-secondary">{{ $log->convert_status }}</span>
                                        @endif
                                    </td>
                                    <td class="small">{{ $log->user?->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No sync activity recorded yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
