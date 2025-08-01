@extends('layout.main')

@section('title_page')
    Create Supplier
@endsection

@section('breadcrumb_title')
    suppliers
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Create Supplier</div>
                </div>
                <div class="card-body">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="type">Type</label>
                            <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="customer" {{ old('type') == 'customer' ? 'selected' : '' }}>Customer</option>
                                <option value="vendor" {{ old('type') == 'vendor' ? 'selected' : '' }}>Vendor</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="code">Code</label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code"
                                name="code" value="{{ old('code') }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="npwp">NPWP</label>
                            <input type="text" class="form-control @error('npwp') is-invalid @enderror" id="npwp"
                                name="npwp" value="{{ old('npwp') }}">
                            @error('npwp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="project">Project</label>
                            <select class="form-control @error('project') is-invalid @enderror" id="project" name="project">
                                <option value="">Select Project</option>
                                <option value="017C" {{ old('project') == '017C' ? 'selected' : '' }}>017C</option>
                                <option value="022C" {{ old('project') == '022C' ? 'selected' : '' }}>022C</option>
                                <option value="021C" {{ old('project') == '021C' ? 'selected' : '' }}>021C</option>
                                <option value="025C" {{ old('project') == '025C' ? 'selected' : '' }}>025C</option>
                                <option value="023C" {{ old('project') == '023C' ? 'selected' : '' }}>023C</option>
                            </select>
                            @error('project')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 