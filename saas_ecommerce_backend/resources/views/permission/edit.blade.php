@extends('layout.app')
@section('title')
    <title>Edit Permission - SAAS Ecommerce Backend</title>
@endsection
@section('content')        
    <div class="row saas-section-anchor" id="tenants">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                  <div>
                    <h5 class="mb-0">Edit Permission</h5>
                    <small class="text-muted">Update the details of an existing permission</small>
                  </div>
                    <div class="d-flex gap-2">
                      <a href="{{ route('permissions.index') }}" class="btn btn-light-secondary btn-sm">
                        <i class="ti ti-arrow-left"></i> Back to Permissions
                      </a>
                    </div>
                </div>
                </div>
                <div class="card-body">
                  <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                      <label for="permission_name" class="form-label">Permission Name</label>
                      <input type="text" class="form-control" id="permission_name" name="name" placeholder="Enter permission name" value="{{ $permission->name }}" required>
                    </div> 
                    <div class='mb-3'>
                      <label for="permission_group" class="form-label">Permission Group</label>
                      <input type="text" class="form-control" id="permission_group" name="group" placeholder="Enter permission group (e.g., User Management, Product Management)" value="{{ $permission->group }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Permission</button>
                    <button type="button" class="btn btn-light-secondary" onclick="window.location='{{ route('permissions.index') }}'">Cancel</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
@endsection