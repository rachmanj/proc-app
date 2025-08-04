@extends('layout.main')

@section('title', 'Import Status')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.dashboard') }}">Consignment</a></li>
    <li class="breadcrumb-item"><a href="{{ route('consignment.imports.upload') }}">Upload</a></li>
    <li class="breadcrumb-item active">Import Status</li>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Import Status for Batch: {{ $batch }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-file-excel"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Items</span>
                                        <span class="info-box-number">{{ $total }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Pending</span>
                                        <span class="info-box-number">{{ $pending ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Processed</span>
                                        <span class="info-box-number">{{ $processed }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-danger">
                                    <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Errors</span>
                                        <span class="info-box-number">{{ $errors }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (isset($pending) && $pending > 0)
                            <div class="alert alert-warning">
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Pending Processing</h5>
                                <p>{{ $pending }} items are pending processing. Click "Process Now" to import the data.
                                </p>
                            </div>
                        @elseif($errors > 0)
                            <div class="alert alert-danger">
                                <h5><i class="icon fas fa-ban"></i> Errors Found</h5>
                                <p>There were {{ $errors }} errors in your import. Please check the details below.</p>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <h5><i class="icon fas fa-check"></i> Success</h5>
                                <p>All {{ $processed }} items were imported successfully!</p>
                            </div>
                        @endif

                        @if (count($items) > 0)
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Items with Errors</h3>
                                </div>
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                            <tr>
                                                <th>Row</th>
                                                <th>Item Code</th>
                                                <th>Description</th>
                                                <th>Error</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($items as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->item_code ?? 'N/A' }}</td>
                                                    <td>{{ $item->item_description ?? 'N/A' }}</td>
                                                    <td>{{ $item->error_message }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        @if (isset($pending) && $pending > 0)
                            <form action="{{ route('consignment.imports.process-batch', $batch) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-play"></i> Process Now
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('consignment.imports.upload') }}" class="btn btn-secondary">
                            <i class="fas fa-upload"></i> Upload Another File
                        </a>
                        <a href="{{ route('consignment.item-prices.index') }}" class="btn btn-info">
                            <i class="fas fa-list"></i> View Item Prices
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
