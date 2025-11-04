@extends('layout.main')

@section('title_page')
    PO Value Report
@endsection

@section('breadcrumb_title')
    <small>Reports / PO / Value Report</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-money-bill-wave mr-2"></i>PO Value Report</h3>
                    <div class="card-tools">
                        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Reports
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('reports.po.value') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Group By</label>
                                    <select name="group_by" class="form-control">
                                        <option value="day" {{ $groupBy == 'day' ? 'selected' : '' }}>Day</option>
                                        <option value="week" {{ $groupBy == 'week' ? 'selected' : '' }}>Week</option>
                                        <option value="month" {{ $groupBy == 'month' ? 'selected' : '' }}>Month</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-filter mr-1"></i>Apply Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Chart -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">PO Value Trends</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="valueChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Table -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Value Summary</h3>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Period</th>
                                                <th class="text-right">PO Count</th>
                                                <th class="text-right">Total Value</th>
                                                <th class="text-right">Average Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $grandTotal = 0;
                                                $grandCount = 0;
                                            @endphp
                                            @foreach($valueData as $item)
                                                @php
                                                    $totalValue = is_object($item) ? $item->total_value : ($item['total_value'] ?? 0);
                                                    $count = is_object($item) ? $item->count : ($item['count'] ?? 0);
                                                    $period = is_object($item) ? $item->period : ($item['period'] ?? '');
                                                    $grandTotal += $totalValue;
                                                    $grandCount += $count;
                                                @endphp
                                                <tr>
                                                    <td>{{ $period }}</td>
                                                    <td class="text-right">{{ number_format($count) }}</td>
                                                    <td class="text-right">{{ number_format($totalValue, 2) }}</td>
                                                    <td class="text-right">{{ number_format($totalValue / ($count ?: 1), 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr class="table-info">
                                                <td><strong>Total</strong></td>
                                                <td class="text-right"><strong>{{ number_format($grandCount) }}</strong></td>
                                                <td class="text-right"><strong>{{ number_format($grandTotal, 2) }}</strong></td>
                                                <td class="text-right"><strong>{{ number_format($grandTotal / ($grandCount ?: 1), 2) }}</strong></td>
                                            </tr>
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

@section('scripts')
    <script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
    <script>
        $(function() {
            const ctx = document.getElementById('valueChart').getContext('2d');
            const valueDataRaw = @json($valueData);
            const valueData = valueDataRaw.map(item => item.total_value || item['total_value'] || 0);
            const valueLabels = valueDataRaw.map(item => item.period || item['period'] || '');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: valueLabels,
                    datasets: [{
                        label: 'PO Value',
                        data: valueData,
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR',
                                        minimumFractionDigits: 0
                                    }).format(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR'
                                    }).format(context.parsed.y);
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
