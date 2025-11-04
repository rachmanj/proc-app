@extends('layout.main')

@section('title_page')
    Approval by Approver Report
@endsection

@section('breadcrumb_title')
    <small>Reports / Approval / By Approver</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-user-check mr-2"></i>Approval by Approver Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.approval.by-approver') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-filter mr-1"></i>Apply Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Approver Statistics</h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Approver Name</th>
                                                <th class="text-right">Approved</th>
                                                <th class="text-right">Rejected</th>
                                                <th class="text-right">Pending</th>
                                                <th class="text-right">Avg Hours</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($approverData as $item)
                                                <tr>
                                                    <td>{{ $item['approver_name'] }}</td>
                                                    <td class="text-right"><span class="badge badge-success">{{ number_format($item['approved_count']) }}</span></td>
                                                    <td class="text-right"><span class="badge badge-danger">{{ number_format($item['rejected_count']) }}</span></td>
                                                    <td class="text-right"><span class="badge badge-warning">{{ number_format($item['pending_count']) }}</span></td>
                                                    <td class="text-right">{{ number_format($item['avg_hours'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
