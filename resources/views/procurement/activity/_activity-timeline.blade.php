<div class="activity-timeline-section mt-4" data-type="{{ $type }}" data-id="{{ $id }}">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>
                Activity Timeline
            </h5>
        </div>
        <div class="card-body">
            <div class="activity-filters mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <select class="form-control form-control-sm" id="activity-event-filter">
                            <option value="">All Events</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control form-control-sm" id="activity-user-filter">
                            <option value="">All Users</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control form-control-sm" id="activity-date-from" placeholder="From">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control form-control-sm" id="activity-date-to" placeholder="To">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-primary" id="apply-filters">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button class="btn btn-sm btn-secondary" id="clear-filters">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </div>
            </div>

            <div class="activity-timeline" id="activity-timeline" style="max-height: 600px; overflow-y: auto;">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin"></i> Loading activity timeline...
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .activity-timeline {
        position: relative;
        padding-left: 30px;
    }

    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }

    .activity-item {
        position: relative;
        padding-bottom: 20px;
        padding-left: 30px;
    }

    .activity-item::before {
        content: '';
        position: absolute;
        left: -25px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #dee2e6;
        z-index: 1;
    }

    .activity-item.commented::before {
        border-color: #2196f3;
        background: #2196f3;
    }

    .activity-item.file_uploaded::before {
        border-color: #4caf50;
        background: #4caf50;
    }

    .activity-item.file_deleted::before {
        border-color: #f44336;
        background: #f44336;
    }

    .activity-item.status_changed::before {
        border-color: #ff9800;
        background: #ff9800;
    }

    .activity-item.assigned::before {
        border-color: #9c27b0;
        background: #9c27b0;
    }

    .activity-item.approval_approved::before {
        border-color: #4caf50;
        background: #4caf50;
    }

    .activity-item.approval_rejected::before {
        border-color: #f44336;
        background: #f44336;
    }

    .activity-item.approval_revision::before {
        border-color: #ff9800;
        background: #ff9800;
    }

    .activity-item.created::before,
    .activity-item.updated::before {
        border-color: #2196f3;
        background: #2196f3;
    }

    .activity-content {
        background: #f8f9fa;
        border-radius: 4px;
        padding: 12px;
        margin-top: 5px;
    }

    .activity-properties {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid #dee2e6;
        font-size: 0.875rem;
        color: #6c757d;
    }

    .activity-properties strong {
        color: #495057;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const type = $('.activity-timeline-section').data('type');
        const id = $('.activity-timeline-section').data('id');
        
        let currentFilters = {};

        loadEvents();
        loadUsers();
        loadActivities();

        function loadEvents() {
            $.ajax({
                url: `{{ route('procurement.activity.events', ['type' => ':type', 'id' => ':id']) }}`
                    .replace(':type', type)
                    .replace(':id', id),
                method: 'GET',
                success: function(events) {
                    const select = $('#activity-event-filter');
                    events.forEach(function(event) {
                        select.append(`<option value="${event}">${formatEventName(event)}</option>`);
                    });
                }
            });
        }

        function loadUsers() {
            $.ajax({
                url: `{{ route('procurement.activity.users', ['type' => ':type', 'id' => ':id']) }}`
                    .replace(':type', type)
                    .replace(':id', id),
                method: 'GET',
                success: function(users) {
                    const select = $('#activity-user-filter');
                    users.forEach(function(user) {
                        select.append(`<option value="${user.id}">${user.name}</option>`);
                    });
                }
            });
        }

        function loadActivities() {
            const params = new URLSearchParams(currentFilters);
            $.ajax({
                url: `{{ route('procurement.activity.index', ['type' => ':type', 'id' => ':id']) }}?${params.toString()}`
                    .replace(':type', type)
                    .replace(':id', id),
                method: 'GET',
                success: function(activities) {
                    renderActivities(activities);
                },
                error: function() {
                    $('#activity-timeline').html(
                        '<div class="text-center text-danger py-4">Error loading activity timeline</div>'
                    );
                }
            });
        }

        function renderActivities(activities) {
            if (activities.length === 0) {
                $('#activity-timeline').html(
                    '<div class="text-center text-muted py-4">No activities found</div>'
                );
                return;
            }

            // Group activities by date
            const grouped = {};
            activities.forEach(function(activity) {
                const date = moment(activity.created_at).format('YYYY-MM-DD');
                if (!grouped[date]) {
                    grouped[date] = [];
                }
                grouped[date].push(activity);
            });

            let html = '';
            Object.keys(grouped).sort().reverse().forEach(function(date) {
                const activitiesForDate = grouped[date];
                html += `<div class="activity-date-header mb-3">
                    <strong>${moment(date).format('MMMM DD, YYYY')}</strong>
                </div>`;
                
                activitiesForDate.forEach(function(activity) {
                    html += renderActivity(activity);
                });
            });

            $('#activity-timeline').html(html);
        }

        function renderActivity(activity) {
            const timeAgo = moment(activity.created_at).fromNow();
            const time = moment(activity.created_at).format('HH:mm');
            const userName = activity.causer ? activity.causer.name : 'System';
            const eventClass = activity.event || 'default';
            
            let icon = getEventIcon(activity.event);
            let description = activity.description || 'Activity';
            
            let propertiesHtml = '';
            if (activity.properties && Object.keys(activity.properties).length > 0) {
                propertiesHtml = '<div class="activity-properties">';
                Object.entries(activity.properties).forEach(function([key, value]) {
                    if (key !== 'comment_id' && key !== 'attachment_id') {
                        const displayKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        propertiesHtml += `<div><strong>${displayKey}:</strong> ${formatPropertyValue(value)}</div>`;
                    }
                });
                propertiesHtml += '</div>';
            }

            return `
                <div class="activity-item ${eventClass}">
                    <div class="activity-content">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <i class="${icon} me-2"></i>
                                <strong>${userName}</strong>
                                <span class="text-muted ms-2">${description}</span>
                            </div>
                            <small class="text-muted">${time} (${timeAgo})</small>
                        </div>
                        ${propertiesHtml}
                    </div>
                </div>
            `;
        }

        function getEventIcon(event) {
            const icons = {
                'commented': 'fas fa-comment text-primary',
                'file_uploaded': 'fas fa-upload text-success',
                'file_deleted': 'fas fa-trash text-danger',
                'status_changed': 'fas fa-exchange-alt text-warning',
                'assigned': 'fas fa-user-check text-purple',
                'approval_approved': 'fas fa-check-circle text-success',
                'approval_rejected': 'fas fa-times-circle text-danger',
                'approval_revision': 'fas fa-sync text-warning',
                'created': 'fas fa-plus-circle text-primary',
                'updated': 'fas fa-edit text-info',
                'followed': 'fas fa-star text-warning',
            };
            return icons[event] || 'fas fa-circle text-secondary';
        }

        function formatEventName(event) {
            return event.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }

        function formatPropertyValue(value) {
            if (typeof value === 'object') {
                return JSON.stringify(value);
            }
            return value;
        }

        $('#apply-filters').on('click', function() {
            currentFilters = {
                event: $('#activity-event-filter').val(),
                user_id: $('#activity-user-filter').val(),
                date_from: $('#activity-date-from').val(),
                date_to: $('#activity-date-to').val(),
            };
            
            // Remove empty filters
            Object.keys(currentFilters).forEach(key => {
                if (!currentFilters[key]) {
                    delete currentFilters[key];
                }
            });
            
            loadActivities();
        });

        $('#clear-filters').on('click', function() {
            $('#activity-event-filter').val('');
            $('#activity-user-filter').val('');
            $('#activity-date-from').val('');
            $('#activity-date-to').val('');
            currentFilters = {};
            loadActivities();
        });
    });
</script>
@endpush

