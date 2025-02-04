@extends('layout.main')

@section('title_page')
    Purchase Request Details
@endsection

@section('breadcrumb_title')
    <small>procurement / purchase request / view</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <x-proc-pr-links page='search' />

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Request Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('procurement.pr.index', ['page' => 'search']) }}" class="btn btn-sm btn-default">
                            Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">PR Number</th>
                                    <td>{{ $purchaseRequest->pr_no }}</td>
                                </tr>
                                <tr>
                                    <th>PR Draft Number</th>
                                    <td>{{ $purchaseRequest->pr_draft_no }}</td>
                                </tr>
                                <tr>
                                    <th>PR Revision Number</th>
                                    <td>{{ $purchaseRequest->pr_rev_no }}</td>
                                </tr>
                                <tr>
                                    <th>Priority</th>
                                    <td>{{ $purchaseRequest->priority }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>{{ $purchaseRequest->pr_status }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">Type</th>
                                    <td>{{ $purchaseRequest->pr_type }}</td>
                                </tr>
                                <tr>
                                    <th>Project Code</th>
                                    <td>{{ $purchaseRequest->project_code }}</td>
                                </tr>
                                <tr>
                                    <th>For Unit</th>
                                    <td>{{ $purchaseRequest->for_unit }}</td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $purchaseRequest->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $purchaseRequest->updated_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Purchase Request Details</h4>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Item Code</th>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>UOM</th>
                                        <th>Open Qty</th>
                                        <th>Status</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($purchaseRequest->details as $index => $detail)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $detail->item_code }}</td>
                                            <td>{{ $detail->item_name }}</td>
                                            <td class="text-right">{{ number_format($detail->quantity) }}</td>
                                            <td>{{ $detail->uom }}</td>
                                            <td class="text-right">{{ number_format($detail->open_qty) }}</td>
                                            <td>{{ $detail->status }}</td>
                                            <td>{{ $detail->line_remarks }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No details found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
