<!-- jQuery -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<!-- Date Range Picker -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- Select2 -->
<script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

<script>
    $(document).ready(function() {
        loadNotifications();
        
        setInterval(function() {
            loadNotifications();
        }, 30000);

        function loadNotifications() {
            $.ajax({
                url: '{{ route("api.notifications.index") }}',
                method: 'GET',
                success: function(response) {
                    updateNotificationUI(response);
                }
            });
        }

        function updateNotificationUI(data) {
            const unreadCount = data.unread_count || 0;
            const notifications = data.notifications || [];

            $('#notificationBadge').text(unreadCount);
            if (unreadCount > 0) {
                $('#notificationBadge').show();
            } else {
                $('#notificationBadge').hide();
            }

            $('#notificationHeader').text(unreadCount + ' Notification' + (unreadCount !== 1 ? 's' : ''));

            let html = '';
            if (notifications.length === 0) {
                html = '<div class="dropdown-item text-center"><span>No notifications</span></div>';
            } else {
                notifications.forEach(function(notification) {
                    const readClass = notification.read_at ? '' : 'font-weight-bold';
                    const timeAgo = getTimeAgo(notification.created_at);
                    html += `
                        <a href="${notification.data.url || '#'}" class="dropdown-item ${readClass}" data-notification-id="${notification.id}">
                            <div class="media">
                                <div class="media-body">
                                    <p class="text-sm mb-0">${notification.data.message || 'Notification'}</p>
                                    <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>${timeAgo}</p>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                    `;
                });
            }
            $('#notificationList').html(html);

            $('[data-notification-id]').on('click', function() {
                const notificationId = $(this).data('notification-id');
                markAsRead(notificationId);
            });
        }

        function markAsRead(notificationId) {
            $.ajax({
                url: '{{ route("api.notifications.read", ":id") }}'.replace(':id', notificationId),
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    loadNotifications();
                }
            });
        }

        $('#markAllReadBtn').on('click', function(e) {
            e.preventDefault();
            $.ajax({
                url: '{{ route("api.notifications.mark-all-read") }}',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    loadNotifications();
                }
            });
        });

        function getTimeAgo(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - date) / 1000);
            
            if (seconds < 60) return 'Just now';
            if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
            if (seconds < 604800) return Math.floor(seconds / 86400) + ' days ago';
            return date.toLocaleDateString();
        }

        // Global AJAX Loading Indicator
        $(document).ajaxStart(function() {
            // Show loading overlay for AJAX requests longer than 300ms
            setTimeout(function() {
                if ($.active > 0) {
                    $('#globalLoadingOverlay').fadeIn(200);
                }
            }, 300);
        });

        $(document).ajaxStop(function() {
            $('#globalLoadingOverlay').fadeOut(200);
        });

        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            $('#globalLoadingOverlay').fadeOut(200);
            
            let errorMessage = 'An error occurred while processing your request.';
            let errorTitle = 'Error';
            
            if (xhr.status === 422) {
                // Validation errors
                errorTitle = 'Validation Error';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorList = '<ul class="text-left mt-2">';
                    Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                        xhr.responseJSON.errors[key].forEach(function(err) {
                            errorList += '<li>' + err + '</li>';
                        });
                    });
                    errorList += '</ul>';
                    errorMessage = errorList;
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
            } else if (xhr.status === 403) {
                errorTitle = 'Access Denied';
                errorMessage = 'You do not have permission to perform this action.';
            } else if (xhr.status === 404) {
                errorTitle = 'Not Found';
                errorMessage = 'The requested resource was not found.';
            } else if (xhr.status === 500) {
                errorTitle = 'Server Error';
                errorMessage = 'A server error occurred. Please try again later or contact support.';
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: errorTitle,
                html: errorMessage,
                confirmButtonColor: '#dc3545',
                allowOutsideClick: false
            });
        });

        // Button loading state helper
        window.showButtonLoading = function(button) {
            const $btn = $(button);
            $btn.addClass('btn-loading');
            $btn.prop('disabled', true);
        };

        window.hideButtonLoading = function(button) {
            const $btn = $(button);
            $btn.removeClass('btn-loading');
            $btn.prop('disabled', false);
        };

        // Form submission with loading state
        $(document).on('submit', 'form[data-ajax-form]', function(e) {
            e.preventDefault();
            const $form = $(this);
            const $submitBtn = $form.find('button[type="submit"], input[type="submit"]');
            
            if ($submitBtn.length) {
                showButtonLoading($submitBtn[0]);
            }
            
            $.ajax({
                url: $form.attr('action'),
                method: $form.attr('method') || 'POST',
                data: $form.serialize(),
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else if (response.message) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 3000,
                            showConfirmButton: false
                        }).then(function() {
                            if (response.reload) {
                                location.reload();
                            }
                        });
                    }
                },
                error: function(xhr) {
                    // Error handled by global ajaxError handler
                },
                complete: function() {
                    if ($submitBtn.length) {
                        hideButtonLoading($submitBtn[0]);
                    }
                }
            });
        });

        // Initialize Bootstrap tooltips
        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
            $('[data-toggle="popover"]').popover();
        });
    });
</script>

@stack('scripts')
