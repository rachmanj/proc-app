@extends('layout.main')

@section('title_page')
    PR by Department Report
@endsection

@section('breadcrumb_title')
    <small>Reports / PR / By Department</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-building mr-2"></i>PR by Department Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Date Filter -->
                    <form method="GET" action="{{ route('reports.pr.by-department') }}" class="mb-4">
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

                    <!-- Department Table -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">PR Distribution by Department</h3>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Department</th>
                                                    @foreach(['OPEN', 'progress', 'approved', 'CLOSED'] as $status)
                                                        <th class="text-center">{{ $status }}</th>
                                                    @endforeach
                                                    <th class="text-center">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($departmentData as $dept => $statuses)
                                                    <tr>
                                                        <td><strong>{{ $dept ?: 'Unknown' }}</strong></td>
                                                        @php
                                                            $total = 0;
                                                            $statusCounts = [];
                                                            foreach($statuses as $status) {
                                                                $statusCounts[$status->pr_status] = $status->count;
                                                                $total += $status->count;
                                                            }
                                                        @endphp
                                                        @foreach(['OPEN', 'progress', 'approved', 'CLOSED'] as $status)
                                                            <td class="text-center">{{ $statusCounts[$status] ?? 0 }}</td>
                                                        @endforeach
                                                        <td class="text-center"><strong>{{ $total }}</strong></td>
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
    </div>
@endsection
