<div class="collaboration-actions mb-3" data-type="{{ $type }}" data-id="{{ $id }}">
    <div class="d-flex flex-wrap gap-2 align-items-center">
        {{-- Follow Button --}}
        <button class="btn btn-sm btn-outline-secondary follow-btn" id="follow-btn">
            <i class="fas fa-star"></i> <span id="follow-text">Follow</span>
            <span class="badge badge-secondary ml-1" id="followers-count">0</span>
        </button>

        {{-- Assignment Section --}}
        @can('assign_document')
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-plus"></i> Assign
            </button>
            <div class="dropdown-menu dropdown-menu-right" style="min-width: 300px; padding: 15px;" id="assign-dropdown-menu">
                <h6 class="dropdown-header">Assign to Buyer</h6>
                <div class="px-2 mb-2">
                    <select class="form-control form-control-sm" id="assign-user-select" style="width: 100%;">
                        <option value="">Select a buyer...</option>
                    </select>
                </div>
                <div class="px-2 mb-2">
                    <textarea class="form-control form-control-sm" id="assign-notes" rows="2" placeholder="Notes (optional)"></textarea>
                </div>
                <div class="px-2">
                    <button class="btn btn-sm btn-primary btn-block" id="assign-submit-btn">
                        <i class="fas fa-check"></i> Assign
                    </button>
                </div>
                <div class="dropdown-divider"></div>
                <h6 class="dropdown-header">Currently Assigned</h6>
                <div id="assignments-list" class="px-2">
                    <div class="text-center text-muted py-2">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </div>
                </div>
            </div>
        </div>
        @endcan

        {{-- Assigned Users Display --}}
        <div id="assigned-users-display" class="d-flex flex-wrap gap-1">
            <!-- Assigned users will be displayed here -->
        </div>
    </div>
</div>

@push('styles')
<style>
    .assigned-user-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 8px;
        background: #e3f2fd;
        border-radius: 12px;
        font-size: 0.875rem;
        margin-right: 4px;
    }
    
    .assigned-user-badge .remove-assignment {
        margin-left: 6px;
        cursor: pointer;
        color: #f44336;
        font-size: 0.75rem;
    }
    
    .assigned-user-badge .remove-assignment:hover {
        color: #d32f2f;
    }
    
    .follow-btn.active {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        const type = $('.collaboration-actions').data('type');
        const id = $('.collaboration-actions').data('id');
        
        // Prevent dropdown from closing when clicking inside it
        // Handle both click and mousedown events
        $('#assign-dropdown-menu').on('click mousedown', function(e) {
            e.stopPropagation();
        });
        
        // Also prevent the select dropdown from closing the parent dropdown
        $(document).on('click mousedown', '#assign-user-select', function(e) {
            e.stopPropagation();
        });
        
        // Prevent textarea from closing dropdown
        $(document).on('click mousedown', '#assign-notes', function(e) {
            e.stopPropagation();
        });
        
        // Prevent all form elements inside dropdown from closing it
        $('#assign-dropdown-menu').find('input, select, textarea, button').on('click mousedown', function(e) {
            e.stopPropagation();
        });
        
        loadFollowStatus();
        loadAssignments();
        loadUsers();

        // Follow/Unfollow functionality
        $('#follow-btn').on('click', function() {
            const isFollowing = $(this).hasClass('active');
            
            $.ajax({
                url: isFollowing 
                    ? `{{ route('procurement.collaboration.unfollow', ['type' => ':type', 'id' => ':id']) }}`
                        .replace(':type', type).replace(':id', id)
                    : `{{ route('procurement.collaboration.follow', ['type' => ':type', 'id' => ':id']) }}`
                        .replace(':type', type).replace(':id', id),
                method: isFollowing ? 'DELETE' : 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    loadFollowStatus();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.error || 'Failed to update follow status'
                    });
                }
            });
        });

        // Load follow status
        function loadFollowStatus() {
            $.ajax({
                url: `{{ route('procurement.collaboration.follow-status', ['type' => ':type', 'id' => ':id']) }}`
                    .replace(':type', type).replace(':id', id),
                method: 'GET',
                success: function(data) {
                    const btn = $('#follow-btn');
                    if (data.following) {
                        btn.addClass('active');
                        $('#follow-text').text('Following');
                    } else {
                        btn.removeClass('active');
                        $('#follow-text').text('Follow');
                    }
                    $('#followers-count').text(data.followers_count || 0);
                }
            });
        }

        // Initialize Select2 for buyer selection with AJAX search
        function initializeBuyerSelect() {
            $('#assign-user-select').select2({
                theme: 'bootstrap4',
                placeholder: 'Select a buyer...',
                allowClear: true,
                dropdownParent: $('#assign-dropdown-menu'),
                ajax: {
                    url: '{{ route('procurement.collaboration.buyers') }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term || '',
                            page: params.page || 1
                        };
                    },
                    processResults: function(data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.map(function(user) {
                                return {
                                    id: user.id,
                                    text: user.name + ' (@' + user.username + ')'
                                };
                            }),
                            pagination: {
                                more: data.length === 10
                            }
                        };
                    },
                    cache: true
                },
                minimumInputLength: 0
            });
        }

        // Load users for assignment dropdown (only buyers)
        function loadUsers() {
            // Initialize Select2 if not already initialized
            if (!$('#assign-user-select').hasClass('select2-hidden-accessible')) {
                initializeBuyerSelect();
            }
        }

        // Load assignments
        function loadAssignments() {
            $.ajax({
                url: `{{ route('procurement.collaboration.assignments', ['type' => ':type', 'id' => ':id']) }}`
                    .replace(':type', type).replace(':id', id),
                method: 'GET',
                success: function(assignments) {
                    renderAssignments(assignments);
                }
            });
        }

        // Render assignments
        function renderAssignments(assignments) {
            const list = $('#assignments-list');
            const display = $('#assigned-users-display');
            
            if (assignments.length === 0) {
                list.html('<div class="text-center text-muted py-2">No assignments</div>');
                display.empty();
                return;
            }

            // Dropdown list
            let listHtml = '';
            assignments.forEach(function(assignment) {
                listHtml += `
                    <div class="d-flex justify-content-between align-items-center py-1">
                        <div>
                            <strong>${assignment.name}</strong>
                            ${assignment.pivot.notes ? `<br><small class="text-muted">${assignment.pivot.notes}</small>` : ''}
                        </div>
                        <button class="btn btn-sm btn-link text-danger remove-assignment-btn" data-user-id="${assignment.id}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            });
            list.html(listHtml);

            // Display badges
            let displayHtml = '';
            assignments.forEach(function(assignment) {
                displayHtml += `
                    <span class="assigned-user-badge">
                        <i class="fas fa-user"></i> ${assignment.name}
                        <span class="remove-assignment" data-user-id="${assignment.id}">
                            <i class="fas fa-times"></i>
                        </span>
                    </span>
                `;
            });
            display.html(displayHtml);
        }

        // Assign user
        $('#assign-submit-btn').on('click', function() {
            const userId = $('#assign-user-select').val();
            const notes = $('#assign-notes').val();

            if (!userId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Please select a user',
                    text: 'You must select a user to assign'
                });
                return;
            }

            $.ajax({
                url: `{{ route('procurement.collaboration.assign', ['type' => ':type', 'id' => ':id']) }}`
                    .replace(':type', type).replace(':id', id),
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: userId,
                    notes: notes
                },
                success: function(response) {
                    $('#assign-user-select').val('');
                    $('#assign-notes').val('');
                    
                    // Close the dropdown
                    $('.dropdown-toggle').dropdown('hide');
                    
                    loadAssignments();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.error || 'Failed to assign user'
                    });
                }
            });
        });

        // Remove assignment
        $(document).on('click', '.remove-assignment-btn, .remove-assignment', function() {
            const userId = $(this).data('user-id');
            
            Swal.fire({
                title: 'Remove Assignment?',
                text: 'Are you sure you want to remove this assignment?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, remove it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('procurement.collaboration.unassign', ['type' => ':type', 'id' => ':id', 'userId' => ':userId']) }}`
                            .replace(':type', type).replace(':id', id).replace(':userId', userId),
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            loadAssignments();
                            Swal.fire({
                                icon: 'success',
                                title: 'Removed!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON?.error || 'Failed to remove assignment'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush

