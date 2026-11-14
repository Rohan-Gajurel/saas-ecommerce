@extends('layout.app')
@section('title')
    <title>Edit Role - SAAS Ecommerce Backend</title>
@endsection
@section('content')        
    <div class="row saas-section-anchor" id="tenants">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                  <div>
                    <h5 class="mb-0">Edit Role</h5>
                    <small class="text-muted">Update role details and permissions</small>
                  </div>
                    <div class="d-flex gap-2">
                      <a href="{{ route('roles.index') }}" class="btn btn-light-secondary btn-sm">
                        <i class="ti ti-arrow-left"></i> Back to Roles
                      </a>
                    </div>
                </div>
                </div>
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                <div class="card-body">
                  <form action="{{ route('roles.update', ['id' => $role->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                      <label for="role_name" class="form-label">Role Name</label>
                      <input type="text" class="form-control" id="role_name" name="name"  required value="{{ $role->name }}" readonly>
                    </div> 
                    {{-- Permissions --}}
                    <div class="mb-3">
                        <label for="permissions" class="form-label">Assign Permissions</label>
                        @foreach($permissions as $group => $groupPermissions)
                            <div class="mb-3">
                                <h6>{{ $group }}</h6> <!-- Display group name -->
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($groupPermissions as $permission)
                                        <div class="form-check">
                                            <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}" 
                                            {{ isset($role) && $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                    <button type="button" class="btn btn-light-secondary" onclick="window.location='{{ route('roles.index') }}'">Cancel</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
@endsection