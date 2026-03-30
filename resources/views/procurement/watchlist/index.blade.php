@extends('layout.main')

@section('title_page')
    My Watchlist
@endsection

@section('breadcrumb_title')
    <small>procurement / collaboration / watchlist</small>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-star"></i> Followed Documents ({{ $allFollowed->count() }})
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary">{{ $followedPRs->count() }} PRs</span>
                        <span class="badge badge-success ml-1">{{ $followedPOs->count() }} POs</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($allFollowed->isEmpty())
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> You are not following any documents yet. 
                            Click the <strong>Follow</strong> button on any PR or PO to add it to your watchlist.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">Type</th>
                                        <th>Document</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                        <th>Last Activity</th>
                                        <th style="width: 120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($allFollowed as $item)
                                        <tr>
                                            <td>
                                                @if($item->type === 'pr')
                                                    <span class="badge badge-info">PR</span>
                                                @else
                                                    <span class="badge badge-success">PO</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>
                                                    @if($item->type === 'pr')
                                                        {{ $item->pr_no ?? 'N/A' }}
                                                    @else
                                                        {{ $item->doc_num ?? 'N/A' }}
                                                    @endif
                                                </strong>
                                                <br>
                                                <small class="text-muted">
                                                    @if($item->type === 'pr')
                                                        {{ $item->details->first()->item_name ?? 'No items' }}
                                                    @else
                                                        {{ $item->details->first()->description ?? 'No items' }}
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                @if($item->type === 'pr')
                                                    <span class="badge badge-{{ $item->pr_status === 'open' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($item->pr_status ?? 'N/A') }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-{{ $item->status === 'draft' ? 'warning' : ($item->status === 'approved' ? 'success' : ($item->status === 'submitted' ? 'info' : ($item->status === 'rejected' ? 'danger' : ($item->status === 'revision' ? 'warning' : 'secondary')))) }}">
                                                        {{ ucfirst($item->status ?? 'N/A') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->assignedUsers->isNotEmpty())
                                                    @foreach($item->assignedUsers->take(2) as $user)
                                                        <span class="badge badge-secondary">{{ $user->name }}</span>
                                                    @endforeach
                                                    @if($item->assignedUsers->count() > 2)
                                                        <small class="text-muted">+{{ $item->assignedUsers->count() - 2 }} more</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No assignments</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item->last_activity)
                                                    <div>
                                                        <strong>{{ $item->last_activity->causer->name ?? 'System' }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ ucfirst($item->last_activity->event ?? 'updated') }}
                                                        </small>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $item->last_activity->created_at->diffForHumans() }}
                                                        </small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">No activity</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ $item->type === 'pr' ? route('procurement.pr.show', $item->id) : route('procurement.po.show', $item->id) }}" 
                                                   class="btn btn-sm btn-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button class="btn btn-sm btn-warning unfollow-btn" 
                                                        data-type="{{ $item->type }}" 
                                                        data-id="{{ $item->id }}"
                                                        title="Unfollow">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('.unfollow-btn').on('click', function() {
            const btn = $(this);
            const type = btn.data('type');
            const id = btn.data('id');
            
            Swal.fire({
                title: 'Unfollow Document?',
                text: 'You will no longer receive updates about this document.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, unfollow'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('procurement.collaboration.unfollow', ['type' => ':type', 'id' => ':id']) }}`
                            .replace(':type', type).replace(':id', id),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Unfollowed!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.error || 'Failed to unfollow'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
