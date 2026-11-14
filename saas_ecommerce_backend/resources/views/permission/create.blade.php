@extends('layout.app')
@section('title')
    <title>Create Permission - SAAS Ecommerce Backend</title>
@endsection
@section('content')        
    <div class="row saas-section-anchor" id="tenants">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                  <div>
                    <h5 class="mb-0">Create Permission</h5>
                    <small class="text-muted">Add new permission to your system</small>
                  </div>
                    <div class="d-flex gap-2">
                      <a href="{{ route('permissions.index') }}" class="btn btn-light-secondary btn-sm">
                        <i class="ti ti-arrow-left"></i> Back to Permissions
                      </a>
                    </div>
                </div>
                </div>
                <div class="card-body">
                  <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                      <label for="permission_name" class="form-label">Permission Name</label>
                      <input type="text" class="form-control" id="permission_name" name="name" placeholder="Enter permission name" required>
                    </div> 
                    <div class='mb-3'>
                      <label for="permission_group" class="form-label">Permission Group</label>
                      <input type="text" class="form-control" id="permission_group" name="group" placeholder="Enter permission group (e.g., User Management, Product Management)" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Permission</button>
                    <button type="button" class="btn btn-light-secondary" onclick="window.location='{{ route('permissions.index') }}'">Cancel</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
@endsection