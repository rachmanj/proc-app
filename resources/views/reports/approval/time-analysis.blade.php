@extends('layout.main')

@section('title_page')
    Approval Time Analysis Report
@endsection

@section('breadcrumb_title')
    <small>Reports / Approval / Time Analysis</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-stopwatch mr-2"></i>Approval Time Analysis Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.approval.time-analysis') }}" class="mb-4">
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
                                    <h3 class="card-title">Approval Time by Level</h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Approval Level</th>
                                                <th class="text-right">Count</th>
                                                <th class="text-right">Avg Hours</th>
                                                <th class="text-right">Min Hours</th>
                                                <th class="text-right">Max Hours</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($timeData as $item)
                                                <tr>
                                                    <td>{{ $item['level_name'] }}</td>
                                                    <td class="text-right">{{ number_format($item['count']) }}</td>
                                                    <td class="text-right">{{ number_format($item['avg_hours'], 2) }}</td>
                                                    <td class="text-right">{{ number_format($item['min_hours']) }}</td>
                                                    <td class="text-right">{{ number_format($item['max_hours']) }}</td>
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
