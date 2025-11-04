@extends('layout.main')

@section('title_page')
    PR Approval Time Report
@endsection

@section('breadcrumb_title')
    <small>Reports / PR / Approval Time</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-hourglass-half mr-2"></i>PR Approval Time Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        PR Approval Time analysis is coming soon. This feature will track approval times for Purchase Requests.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
