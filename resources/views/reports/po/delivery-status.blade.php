@extends('layout.main')

@section('title_page')
    PO Delivery Status Report
@endsection

@section('breadcrumb_title')
    <small>Reports / PO / Delivery Status</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-shipping-fast mr-2"></i>PO Delivery Status Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        PO Delivery Status tracking is coming soon. This feature will track delivery status and ETA for Purchase Orders.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
