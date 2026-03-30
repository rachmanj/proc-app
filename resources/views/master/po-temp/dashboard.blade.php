@extends('layout.main')

@section('title_page')
    PO Temporary Data
@endsection


@section('breadcrumb_title')
    <small>
        master / po temp / dashboard
    </small>
@endsection


@section('styles')
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-8">
                        <h4>PO Temporary Data</h4>
                    </div>
                    <div class="col-4 text-right">
                        <button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="modal"
                            data-target="#importModal">
                            <i class="fas fa-file-import mr-2"></i>Import
                        </button>
                        <button type="button" class="btn btn-sm btn-success" id="copyToPOBtn">
                            <i class="fas fa-file-import mr-2"></i>Copy to PO Table
                        </button>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table id="poTempTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>PO No</th>
                            <th>Posting Date</th>
                            <th>Create Date</th>
                            <th>Delivery Date</th>
                            <th>ETA</th>
                            <th>PR No</th>
                            <th>Vendor Code</th>
                            <th>Vendor Name</th>
                            <th>Unit No</th>
                            <th>Item Code</th>
                            <th>Description</th>
                            <th>Remark 1</th>
                            <th>Remark 2</th>
                            <th>Qty</th>
                            <th>Currency</th>
                            <th>Unit Price</th>
                            <th>Item Amount</th>
                            <th>Total PO Price</th>
                            <th>PO with VAT</th>
                            <th>UOM</th>
                            <th>Project Code</th>
                            <th>Dept Code</th>
                            <th>PO Status</th>
                            <th>Delivery Status</th>
                            <th>Budget Type</th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import PO Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="importForm" method="POST" action="{{ route('master.potemp.import') }}"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="file">Choose Excel File</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx, .xls"
                                required>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                File Excel harus memiliki header kolom yang sesuai.
                                <strong>NEW:</strong> Kolom <code>remark1</code> dan <code>remark2</code> telah ditambahkan
                                setelah kolom <code>description</code>.
                                <br>
                                <i class="fas fa-download"></i>
                                <a href="#" onclick="showImportFormat()" class="text-primary">Lihat format import yang
                                    diperlukan</a>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Format Modal -->
    <div class="modal fade" id="importFormatModal" tabindex="-1" role="dialog" aria-labelledby="importFormatModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importFormatModalLabel">
                        <i class="fas fa-file-excel text-success"></i> Format Import PO Temp
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Update Terbaru:</strong> Kolom <code>remark1</code> dan <code>remark2</code> telah
                        ditambahkan setelah kolom <code>description</code>.
                    </div>

                    <h6><i class="fas fa-list"></i> Kolom Excel yang Diperlukan:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Column Name</th>
                                    <th>Required</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td><code>po_no</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Purchase Order Number</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td><code>posting_date</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Posting Date (DD/MM/YYYY)</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td><code>create_date</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Create Date (DD/MM/YYYY)</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td><code>po_delivery_date</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>PO Delivery Date (DD/MM/YYYY)</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td><code>po_eta</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>PO ETA (DD/MM/YYYY)</td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td><code>pr_no</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Purchase Request Number</td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td><code>vendor_code</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Vendor Code</td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td><code>vendor_name</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Vendor Name</td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td><code>unit_no</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Unit Number</td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td><code>item_code</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Item Code</td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td><code>description</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Item Description</td>
                                </tr>
                                <tr class="table-success">
                                    <td>12</td>
                                    <td><code>remark1</code></td>
                                    <td><span class="badge badge-success">No</span></td>
                                    <td><strong>Remark 1 (NEW)</strong></td>
                                </tr>
                                <tr class="table-success">
                                    <td>13</td>
                                    <td><code>remark2</code></td>
                                    <td><span class="badge badge-success">No</span></td>
                                    <td><strong>Remark 2 (NEW)</strong></td>
                                </tr>
                                <tr>
                                    <td>14</td>
                                    <td><code>qty</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Quantity</td>
                                </tr>
                                <tr>
                                    <td>15</td>
                                    <td><code>po_currency</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>PO Currency</td>
                                </tr>
                                <tr>
                                    <td>16</td>
                                    <td><code>unit_price</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Unit Price</td>
                                </tr>
                                <tr>
                                    <td>17</td>
                                    <td><code>item_amount</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Item Amount</td>
                                </tr>
                                <tr>
                                    <td>18</td>
                                    <td><code>total_po_price</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Total PO Price</td>
                                </tr>
                                <tr>
                                    <td>19</td>
                                    <td><code>po_with_vat</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>PO with VAT</td>
                                </tr>
                                <tr>
                                    <td>20</td>
                                    <td><code>uom</code></td>
                                    <td><span class="badge badge-success">No</span></td>
                                    <td>Unit of Measure</td>
                                </tr>
                                <tr>
                                    <td>21</td>
                                    <td><code>project_code</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Project Code</td>
                                </tr>
                                <tr>
                                    <td>22</td>
                                    <td><code>dept_code</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Department Code</td>
                                </tr>
                                <tr>
                                    <td>23</td>
                                    <td><code>po_status</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>PO Status</td>
                                </tr>
                                <tr>
                                    <td>24</td>
                                    <td><code>po_delivery_status</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>PO Delivery Status</td>
                                </tr>
                                <tr>
                                    <td>25</td>
                                    <td><code>budget_type</code></td>
                                    <td><span class="badge badge-danger">Yes</span></td>
                                    <td>Budget Type</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Penting:</strong>
                        <ul class="mb-0">
                            <li>Header kolom harus <strong>case-sensitive</strong> dan persis sama</li>
                            <li>Format tanggal harus <strong>DD/MM/YYYY</strong></li>
                            <li>File harus memiliki header row di baris pertama</li>
                            <li>Kolom <code>remark1</code> dan <code>remark2</code> boleh kosong</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Set up CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable
            var table = $('#poTempTable').DataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: "{{ route('master.potemp.data') }}",
                columns: [{
                        data: 'po_no',
                        name: 'po_no'
                    },
                    {
                        data: 'posting_date',
                        name: 'posting_date'
                    },
                    {
                        data: 'create_date',
                        name: 'create_date'
                    },
                    {
                        data: 'po_delivery_date',
                        name: 'po_delivery_date'
                    },
                    {
                        data: 'po_eta',
                        name: 'po_eta'
                    },
                    {
                        data: 'pr_no',
                        name: 'pr_no'
                    },
                    {
                        data: 'vendor_code',
                        name: 'vendor_code'
                    },
                    {
                        data: 'vendor_name',
                        name: 'vendor_name'
                    },
                    {
                        data: 'unit_no',
                        name: 'unit_no'
                    },
                    {
                        data: 'item_code',
                        name: 'item_code'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'remark1',
                        name: 'remark1'
                    },
                    {
                        data: 'remark2',
                        name: 'remark2'
                    },
                    {
                        data: 'qty',
                        name: 'qty'
                    },
                    {
                        data: 'po_currency',
                        name: 'po_currency'
                    },
                    {
                        data: 'unit_price',
                        name: 'unit_price'
                    },
                    {
                        data: 'item_amount',
                        name: 'item_amount'
                    },
                    {
                        data: 'total_po_price',
                        name: 'total_po_price'
                    },
                    {
                        data: 'po_with_vat',
                        name: 'po_with_vat'
                    },
                    {
                        data: 'uom',
                        name: 'uom'
                    },
                    {
                        data: 'project_code',
                        name: 'project_code'
                    },
                    {
                        data: 'dept_code',
                        name: 'dept_code'
                    },
                    {
                        data: 'po_status',
                        name: 'po_status'
                    },
                    {
                        data: 'po_delivery_status',
                        name: 'po_delivery_status'
                    },
                    {
                        data: 'budget_type',
                        name: 'budget_type'
                    }
                ]
            });

            // Handle Import Form Submit
            $('#importForm').on('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                Swal.fire({
                    title: 'Importing...',
                    text: 'Please wait while we import your data.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#importModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                table.ajax.reload();
                            }
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON ? xhr.responseJSON.message :
                                'An error occurred while importing the data.',
                            showConfirmButton: true
                        });
                    }
                });
            });

            // Handle Import to PO Table
            $('#copyToPOBtn').on('click', function() {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will copy the temporary data to the PO table.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',

                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, copy it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we process your request.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('master.potemp.copy-to-po') }}",
                            type: 'POST',
                            success: function(response) {
                                if (response.reload_page) {
                                    // Reload the page to show flash messages
                                    window.location.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: response.message,
                                        showConfirmButton: true
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            table.ajax.reload();
                                        }
                                    });
                                }
                            },
                            error: function(xhr) {
                                if (xhr.responseJSON && xhr.responseJSON.reload_page) {
                                    // Reload the page to show flash messages
                                    window.location.reload();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: xhr.responseJSON ? xhr
                                            .responseJSON
                                            .message :
                                            'An error occurred while copying the data.',
                                        showConfirmButton: true
                                    });
                                }
                            }
                        });
                    }
                });
            });

            // Function to show import format modal
            window.showImportFormat = function() {
                $('#importFormatModal').modal('show');
            };
        });
    </script>
@endsection
