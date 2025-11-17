<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success">
                <h5 class="mb-0"><i class="fas fa-shopping-cart mr-2"></i>Purchase Order (PO) Sync</h5>
            </div>
            <div class="card-body">
                <!-- Last Sync Info -->
                @if($lastSync)
                    <div class="alert alert-info">
                        <strong>Last Sync:</strong> 
                        {{ $lastSync->created_at->setTimezone('Asia/Jakarta')->format('d M Y H:i:s') }} (UTC+8)
                        <br>
                        <strong>Date Range:</strong> {{ $lastSync->start_date->format('d M Y') }} - {{ $lastSync->end_date->format('d M Y') }}
                        <br>
                        <strong>Results:</strong> Synced: {{ $lastSync->records_synced }}, Created: {{ $lastSync->records_created }}, Skipped: {{ $lastSync->records_skipped }}
                        @if($lastSync->convert_status === 'failed')
                            <br><span class="text-danger"><strong>Conversion Failed:</strong> {{ $lastSync->error_message }}</span>
                        @endif
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i> No sync history found
                    </div>
                @endif

                <!-- Date Range Selection -->
                <div class="form-group">
                    <label>Select Date Range</label>
                    <div class="btn-group mb-2" role="group">
                        <button type="button" class="btn btn-sm btn-outline-success" id="po-today-btn">TODAY</button>
                        <button type="button" class="btn btn-sm btn-outline-success" id="po-yesterday-btn">YESTERDAY</button>
                        @can('sync-custom-date')
                            <button type="button" class="btn btn-sm btn-outline-success" id="po-custom-btn">CUSTOM</button>
                        @endcan
                    </div>
                    <div id="po-custom-date-range" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="po-start-date">Start Date</label>
                                <input type="date" class="form-control" id="po-start-date" name="start_date">
                            </div>
                            <div class="col-md-6">
                                <label for="po-end-date">End Date</label>
                                <input type="date" class="form-control" id="po-end-date" name="end_date">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="po-selected-start-date">
                    <input type="hidden" id="po-selected-end-date">
                </div>

                <!-- Action Buttons -->
                <div class="form-group">
                    <button type="button" class="btn btn-success" id="po-sync-btn">
                        <i class="fas fa-sync mr-2"></i>Sync PO from SAP
                    </button>
                    <button type="button" class="btn btn-danger ml-2" id="po-truncate-btn">
                        <i class="fas fa-trash mr-2"></i>Clear PO Temp Table
                    </button>
                </div>

                <!-- Results Display -->
                <div id="po-results" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Set timezone to UTC+8 (Asia/Jakarta)
    const timezone = 'Asia/Jakarta';
    
    function getTodayUTC8() {
        return new Date(new Date().toLocaleString("en-US", {timeZone: timezone}));
    }
    
    function formatDateForInput(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // TODAY button
    $('#po-today-btn').on('click', function() {
        const today = getTodayUTC8();
        const dateStr = formatDateForInput(today);
        $('#po-selected-start-date').val(dateStr);
        $('#po-selected-end-date').val(dateStr);
        $('#po-custom-date-range').hide();
        $(this).addClass('active').siblings().removeClass('active');
    });

    // YESTERDAY button
    $('#po-yesterday-btn').on('click', function() {
        const yesterday = getTodayUTC8();
        yesterday.setDate(yesterday.getDate() - 1);
        const dateStr = formatDateForInput(yesterday);
        $('#po-selected-start-date').val(dateStr);
        $('#po-selected-end-date').val(dateStr);
        $('#po-custom-date-range').hide();
        $(this).addClass('active').siblings().removeClass('active');
    });

    // CUSTOM button
    $('#po-custom-btn').on('click', function() {
        $('#po-custom-date-range').toggle();
        $(this).toggleClass('active');
    });

    // Custom date inputs
    $('#po-start-date, #po-end-date').on('change', function() {
        $('#po-selected-start-date').val($('#po-start-date').val());
        $('#po-selected-end-date').val($('#po-end-date').val());
        $('#po-custom-btn').addClass('active');
    });

    // Sync button
    $('#po-sync-btn').on('click', function() {
        const startDate = $('#po-selected-start-date').val();
        const endDate = $('#po-selected-end-date').val();

        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Date Range Required',
                text: 'Please select a date range first',
            });
            return;
        }

        Swal.fire({
            title: 'Syncing PO from SAP...',
            text: 'Please wait while we fetch and process data.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "{{ route('master.sync-with-sap.sync-po') }}",
            type: 'POST',
            data: {
                start_date: startDate,
                end_date: endDate,
            },
            success: function(response) {
                Swal.fire({
                    icon: response.success ? 'success' : 'warning',
                    title: response.success ? 'Success!' : 'Warning',
                    html: response.message,
                    showConfirmButton: true
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred while syncing data.',
                    showConfirmButton: true
                });
            }
        });
    });

    // Truncate button
    $('#po-truncate-btn').on('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will clear all data in the PO temporary table.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, clear it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('master.sync-with-sap.truncate-po') }}",
                    type: 'POST',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred.',
                        });
                    }
                });
            }
        });
    });

    // Set default to TODAY
    $('#po-today-btn').click();
});
</script>
@endpush

