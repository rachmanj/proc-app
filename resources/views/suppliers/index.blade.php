@extends('layout.main')

@section('title_page')
    Suppliers
@endsection

@section('breadcrumb_title')
    suppliers
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Suppliers</div>
                    <a href="{{ route('suppliers.create') }}" class="btn btn-sm btn-primary float-right">
                        <i class="fas fa-plus"></i> Add Supplier
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-sm" id="suppliers">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>NPWP</th>
                                <th>Project</th>
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
    <script>
        $(function() {
            $('#suppliers').DataTable({
                processing: true,
                serverSide: false,
                ajax: "{{ route('suppliers.data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'code',
                        name: 'code'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'npwp',
                        name: 'npwp'
                    },
                    {
                        data: 'project',
                        name: 'project'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        className: 'text-center',
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush