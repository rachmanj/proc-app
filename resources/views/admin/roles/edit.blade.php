@extends('layout.main')

@section('title_page')
    Edit Role
@endsection

@section('breadcrumb_title')
    roles
@endsection

@section('content')
    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <div class="card-title">Edit Role</div>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-primary float-right">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Role Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $role->name }}"
                                        autofocus>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="guard_name">Guard Name</label>
                                    <input type="text" name="guard_name" class="form-control"
                                        value="{{ $role->guard_name }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-sm">Update Role</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <div class="card-title">Assign Permissions to This Role</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="permissions">Permissions</label>
                            <div>
                                @foreach ($permissions as $permission)
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" class="form-check-input"
                                            value="{{ $permission->id }}"
                                            {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission-{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
