@extends('layout.main')

@section('title_page')
    Roles
@endsection

@section('breadcrumb_title')
    roles
@endsection

@section('content')
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <div class="card-title">New Role</div>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-primary float-right">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Role Name</label>
                                    <input type="text" name="name" class="form-control" autofocus>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="guard_name">Guard Name</label>
                                    <input type="text" name="guard_name" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-primary"><i class="fas fa-save"></i> Save</button>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <div class="card-title">Assign Permissions to This Role</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                @if ($permissions)
                                    <div class="form-group">
                                        <label for="permissions">Permissions</label>
                                        <div id="permissions">
                                            @foreach ($permissions as $permission)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                                        value="{{ $permission->id }}" id="permission{{ $permission->id }}">
                                                    <label class="form-check-label" for="permission{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
