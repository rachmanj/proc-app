@extends('layout.main')

@section('title_page')
    Change Password
@endsection

@section('breadcrumb_title')
    change password
@endsection

@section('content')
    <div class="row">
        <div class="col-6">
            <div class="card card-secondary">
                <div class="card-header">
                    <div class="card-title">Change Password for: {{ $user->name }}</div>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary float-right">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.users.password_update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        @if(auth()->id() == $user->id)
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" name="current_password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   required>
                            @error('current_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        @endif

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" name="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   required>
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirm New Password</label>
                            <input type="password" name="password_confirmation" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   required>
                            @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 