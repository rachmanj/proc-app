@extends('layout.main')

@section('title', 'Warehouses')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.dashboard') }}">Consignment</a></li>
    <li class="breadcrumb-item active">Warehouses</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Warehouses</h3>
                        <div class="card-tools">
                            @can('crud_consignment')
                                <a href="{{ route('consignment.warehouses.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Warehouse
                                </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warehouses as $warehouse)
                                    <tr>
                                        <td>{{ $warehouse->id }}</td>
                                        <td>{{ $warehouse->name }}</td>
                                        <td>{{ $warehouse->code }}</td>
                                        <td>{{ Str::limit($warehouse->description, 50) }}</td>
                                        <td>{{ $warehouse->created_at->format('d-m-Y') }}</td>
                                        <td>
                                            <a href="{{ route('consignment.warehouses.show', $warehouse->id) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @can('crud_consignment')
                                                <a href="{{ route('consignment.warehouses.edit', $warehouse->id) }}"
                                                    class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('consignment.warehouses.destroy', $warehouse->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Are you sure you want to delete this warehouse?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No warehouses found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $warehouses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
