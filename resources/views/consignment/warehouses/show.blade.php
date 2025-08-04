@extends('layout.main')

@section('title', 'Warehouse Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.dashboard') }}">Consignment</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.warehouses.index') }}">Warehouses</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Warehouse Details</h3>
                        <div class="card-tools">
                            @can('crud_consignment')
                                <a href="{{ route('consignment.warehouses.edit', $warehouse->id) }}"
                                    class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('consignment.warehouses.destroy', $warehouse->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete this warehouse?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 30%">ID</th>
                                <td>{{ $warehouse->id }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $warehouse->name }}</td>
                            </tr>
                            <tr>
                                <th>Code</th>
                                <td>{{ $warehouse->code }}</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>{{ $warehouse->description ?? 'No description provided' }}</td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $warehouse->created_at->format('d-m-Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ $warehouse->updated_at->format('d-m-Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('consignment.warehouses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
