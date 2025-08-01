@extends('layout.main')

@section('title_page')
    PO Service
@endsection

@section('breadcrumb_title')
    po_service
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">PO Service List</div>
                    <div class="card-tools">
                        <a href="{{ route('po_service.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create New
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-sm" id="po-service-table" >
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>PO No</th>
                                <th>Date</th>
                                <th>Vendor</th>
                                <th>Project</th>
                                <th>VAT</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Moment.js -->
    <script src="{{ asset('adminlte/plugins/moment/moment.min.js') }}"></script>
    <script>
        $(function() {
            $('#po-service-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('po_service.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'po_no',
                        name: 'po_no'
                    },
                    {
                        data: 'date',
                        name: 'date',
                        render: function(data) {
                            return moment(data).format('DD-MM-YYYY');
                        }
                    },
                    {
                        data: 'supplier_name',
                        name: 'supplier_name'
                    },
                    {
                        data: 'project_code',
                        name: 'project_code'
                    },
                    {
                        data: 'is_vat',
                        name: 'is_vat',
                        render: function(data) {
                            return data ? 'Yes' : 'No';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush 