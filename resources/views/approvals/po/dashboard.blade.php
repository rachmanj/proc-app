@extends('layout.main')

@section('title_page')
    Approvals
@endsection

@section('breadcrumb_title')
    <small>
        approvals / purchase order / dashboard
    </small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-aprv-po-links page="dashboard" />

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Orders Pending Approval</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>PO Number</th>
                                    <th>Supplier</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <!-- <th>Level 1</th>
                                    <th>Level 2</th> -->
                                    
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchaseOrders as $po)
                                    <tr>
                                        <td>{{ $po->doc_num }}</td>
                                        <td>{{ $po->supplier->name ?? '-' }}</td>
                                        <td>{{ $po->doc_date->format('d M Y') }}</td>
                                        <td>
                                            @php
                                                $badgeClass = $po->status === 'approved' ? 'success' : 
                                                            ($po->status === 'rejected' ? 'danger' : 
                                                            ($po->status === 'revision' ? 'warning' : 'info'));
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">{{ ucfirst($po->status) }}</span>
                                        </td>
                                        <!-- <td>
                                            @php
                                                $level1Approval = $po->approvals->where('approval_level.level', 1)->first();
                                                $status = $level1Approval ? $level1Approval->status : 'pending';
                                                $badgeClass = $status === 'approved' ? 'success' : 
                                                            ($status === 'rejected' ? 'danger' : 
                                                            ($status === 'revision' ? 'warning' : 'info'));
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                        </td> -->
                                        <!-- <td>
                                            @php
                                                $level2Approval = $po->approvals->where('approval_level.level', 2)->first();
                                                $status = $level2Approval ? $level2Approval->status : 'pending';
                                                $badgeClass = $status === 'approved' ? 'success' : 
                                                            ($status === 'rejected' ? 'danger' : 
                                                            ($status === 'revision' ? 'warning' : 'info'));
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">{{ ucfirst($status) }}</span>
                                        </td> -->
                                        
                                        <td class="text-center">
                                            <a href="{{ route('approvals.po.show', $po) }}" class="btn btn-xs btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No purchase orders pending approval</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.table').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
@endsection
