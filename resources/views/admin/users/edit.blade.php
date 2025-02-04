@extends('layout.main')

@section('title_page')
    Edit User
@endsection

@section('breadcrumb_title')
    users
@endsection

@section('content')
    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-7">
                <div class="card card-secondary">
                    <div class="card-header">
                        <div class="card-title">Edit User</div>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary float-right">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" autofocus>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" class="form-control" value="{{ $user->username }}">
                        </div>
                        <div class="form-group">
                            <label for="nik">NIK</label>
                            <input type="text" name="nik" class="form-control" value="{{ $user->nik }}">
                        </div>
                        <div class="form-group">
                            <label for='project'>Project</label>
                            <select name="project" class="form-control select2bs4">
                                <option value="">-- Select Project --</option>
                                @foreach (\App\Models\Project::orderBy('code')->get() as $project)
                                    <option value="{{ $project->code }}"
                                        {{ $user->project == $project->code ? 'selected' : '' }}>{{ $project->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for='department_id'>Department</label>
                            <select name="department_id" class="form-control select2bs4">
                                <option value="">-- Select Department --</option>
                                @foreach (\App\Models\Department::orderBy('department_name')->get() as $department)
                                    <option value="{{ $department->id }}"
                                        {{ $user->department_id == $department->id ? 'selected' : '' }}>
                                        {{ $department->department_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-5">
                <div class="card card-secondary">
                    <div class="card-header">
                        <div class="card-title">Assign Roles</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            @foreach ($roles as $role)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="role-{{ $role->id }}"
                                        name="roles[]" value="{{ $role->id }}"
                                        {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                                    <label class="form-check-label"
                                        for="role-{{ $role->id }}">{{ $role->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card card-secondary mt-4">
                    <div class="card-header">
                        <div class="card-title">Assign Approval Levels</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            @foreach (\App\Models\ApprovalLevel::orderBy('level')->get() as $level)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="approval-level-{{ $level->id }}"
                                        name="approval_levels[]" value="{{ $level->id }}"
                                        {{ $user->approvers->contains('approval_level_id', $level->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="approval-level-{{ $level->id }}">
                                        {{ $level->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-success btn-sm">Update User</button>
            </div>
        </div>
    </form>
@endsection

@section('styles')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('scripts')
    <!-- Select2 -->
    <script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
@endsection
