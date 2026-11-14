@extends('layout.app')
@section('title')
    <title>Edit Category - SAAS Ecommerce Backend</title>
@endsection
@section('content')        
    <div class="row saas-section-anchor" id="tenants">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                  <div>
                    <h5 class="mb-0">Edit Category</h5>
                    <small class="text-muted">Update category details</small>
                  </div>
                    <div class="d-flex gap-2">
                      <a href="{{ route('categories.index') }}" class="btn btn-light-secondary btn-sm">
                        <i class="ti ti-arrow-left"></i> Back to Categories
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
                  <form action="{{ route('categories.update', ['id' => $category->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                      <label for="category_name" class="form-label">Category Name</label>
                      <input type="text" class="form-control" id="category_name" name="name"  required value="{{ $category->name }}" readonly>
                    </div> 
                    {{-- Image --}}
                    <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image">
                    </div>
                    {{-- Parent Category --}}
                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Category (Top Level)</label>
                        <select class="form-control" id="parent_id" name="parent_id">
                            <option value="">Select Top Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $category->parent_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                    <button type="button" class="btn btn-light-secondary" onclick="window.location='{{ route('categories.index') }}'">Cancel</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
@endsection