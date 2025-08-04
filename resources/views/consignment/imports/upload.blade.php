@extends('layout.main')

@section('title', 'Upload Item Prices')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.dashboard') }}">Consignment</a></li>
    <li class="breadcrumb-item active">Upload</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Upload Item Prices from Excel</h3>
                    </div>
                    <form action="{{ route('consignment.imports.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-info"></i> Instructions</h5>
                                <ol>
                                    <li>Download the template file using the button below.</li>
                                    <li>Fill in the required information in the Excel file.</li>
                                    <li>Save the file and upload it using the form below.</li>
                                    <li>Click "Upload" to process the file.</li>
                                </ol>
                                <p>
                                    <strong>Note:</strong> The system will validate the data and report any errors. All
                                    valid items will be imported.
                                </p>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
                                        <select class="form-control select2 @error('supplier_id') is-invalid @enderror"
                                            id="supplier_id" name="supplier_id" required>
                                            <option value="">-- Select Supplier --</option>
                                            @foreach (\App\Models\Supplier::orderBy('name')->get() as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">This supplier will be applied to all imported
                                            items</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="project">Project</label>
                                        <input type="text" class="form-control @error('project') is-invalid @enderror"
                                            id="project" name="project" placeholder="Enter project code">
                                        @error('project')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">Optional: Will be applied to all items if
                                            provided</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="warehouse_id">Warehouse</label>
                                        <select class="form-control select2 @error('warehouse_id') is-invalid @enderror"
                                            id="warehouse_id" name="warehouse_id">
                                            <option value="">-- Select Warehouse --</option>
                                            @foreach (\App\Models\Warehouse::orderBy('name')->get() as $warehouse)
                                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('warehouse_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">Optional: Will be applied to all items if
                                            selected</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                            id="start_date" name="start_date">
                                        @error('start_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">Optional: Uses upload date if not
                                            provided</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="expired_date">Expiry Date</label>
                                        <input type="date"
                                            class="form-control @error('expired_date') is-invalid @enderror"
                                            id="expired_date" name="expired_date">
                                        @error('expired_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">Optional: No expiry if not provided</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="excel_file">Excel File <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file"
                                            class="custom-file-input @error('excel_file') is-invalid @enderror"
                                            id="excel_file" name="excel_file" accept=".xlsx,.xls,.csv" required>
                                        <label class="custom-file-label" for="excel_file">Choose file</label>
                                    </div>
                                </div>
                                @error('excel_file')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">Accepted formats: .xlsx, .xls, .csv</small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload
                            </button>
                            <a href="{{ route('consignment.imports.template') }}" class="btn btn-secondary">
                                <i class="fas fa-download"></i> Download Template
                            </a>
                            <a href="{{ route('consignment.item-prices.index') }}" class="btn btn-default float-right">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Template Format</h3>
                    </div>
                    <div class="card-body">
                        <p>The Excel file should contain the following columns:</p>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Column</th>
                                    <th>Description</th>
                                    <th>Required</th>
                                    <th>Format</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>supplier_id</td>
                                    <td>Supplier ID from the system</td>
                                    <td>No (provided in form)</td>
                                    <td>Number</td>
                                </tr>
                                <tr>
                                    <td>item_code</td>
                                    <td>Unique code for the item</td>
                                    <td>No</td>
                                    <td>Text</td>
                                </tr>
                                <tr>
                                    <td>item_description</td>
                                    <td>Description of the item</td>
                                    <td>No</td>
                                    <td>Text</td>
                                </tr>
                                <tr>
                                    <td>part_number</td>
                                    <td>Part number of the item</td>
                                    <td>No</td>
                                    <td>Text</td>
                                </tr>
                                <tr>
                                    <td>brand</td>
                                    <td>Brand of the item</td>
                                    <td>No</td>
                                    <td>Text</td>
                                </tr>
                                <tr>
                                    <td>project</td>
                                    <td>Project code</td>
                                    <td>No (provided in form)</td>
                                    <td>Text</td>
                                </tr>
                                <tr>
                                    <td>warehouse</td>
                                    <td>Warehouse name</td>
                                    <td>No (provided in form)</td>
                                    <td>Text</td>
                                </tr>
                                <tr>
                                    <td>start_date</td>
                                    <td>Start date for the price</td>
                                    <td>No (defaults to upload date)</td>
                                    <td>Date (YYYY-MM-DD)</td>
                                </tr>
                                <tr>
                                    <td>expired_date</td>
                                    <td>Expiry date for the price</td>
                                    <td>No (provided in form)</td>
                                    <td>Date (YYYY-MM-DD)</td>
                                </tr>
                                <tr>
                                    <td>uom</td>
                                    <td>Unit of Measure</td>
                                    <td>Yes</td>
                                    <td>Text</td>
                                </tr>
                                <tr>
                                    <td>qty</td>
                                    <td>Quantity</td>
                                    <td>Yes</td>
                                    <td>Number</td>
                                </tr>
                                <tr>
                                    <td>price</td>
                                    <td>Price in IDR</td>
                                    <td>Yes</td>
                                    <td>Number</td>
                                </tr>
                                <tr>
                                    <td>description</td>
                                    <td>Additional description</td>
                                    <td>No</td>
                                    <td>Text</td>
                                </tr>
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
            // Show filename in custom file input
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    </script>
@endsection
