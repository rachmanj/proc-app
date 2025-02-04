<div class="mt-4">
    <h3>Approval Status: {{ ucfirst($purchaseOrder->status) }}</h3>

    @if ($purchaseOrder->status === 'draft')
        <form action="{{ route('purchase-orders.submit', $purchaseOrder) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="btn btn-primary">Submit for Approval</button>
        </form>
    @endif

    @if ($purchaseOrder->status === 'submitted' || $purchaseOrder->status === 'approved_level_1')
        @php
            $currentApproval = $purchaseOrder->approvals()->where('status', 'pending')->first();
            $userIsApprover = auth()
                ->user()
                ->approvers()
                ->where('approval_level_id', $currentApproval->approval_level_id)
                ->exists();
        @endphp

        @if ($userIsApprover)
            <div class="mt-4">
                <form action="{{ route('purchase-orders.approve', $purchaseOrder) }}" method="POST" class="inline">
                    @csrf
                    <div class="form-group">
                        <label for="notes">Notes (optional)</label>
                        <textarea name="notes" id="notes" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Approve</button>
                </form>

                <form action="{{ route('purchase-orders.reject', $purchaseOrder) }}" method="POST" class="inline">
                    @csrf
                    <div class="form-group">
                        <label for="notes">Rejection Notes (optional)</label>
                        <textarea name="notes" id="notes" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </form>
            </div>
        @endif
    @endif

    <div class="mt-4">
        <h4>Approval History</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Level</th>
                    <th>Status</th>
                    <th>Approver</th>
                    <th>Notes</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchaseOrder->approvals as $approval)
                    <tr>
                        <td>{{ $approval->approvalLevel->name }}</td>
                        <td>{{ ucfirst($approval->status) }}</td>
                        <td>{{ $approval->approver->user->name ?? 'Pending' }}</td>
                        <td>{{ $approval->notes }}</td>
                        <td>{{ $approval->approved_at ? $approval->approved_at->format('Y-m-d H:i') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
