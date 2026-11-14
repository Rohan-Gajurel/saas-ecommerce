@extends('layout.app')

@section('title')
    <title>Edit Brand - SAAS Ecommerce Backend</title>
@endsection

@section('content')
    <div class="row saas-section-anchor" id="all-brands">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0">Edit Brand</h5>
                            <small class="text-muted">Update brand information</small>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('brands.index') }}" class="btn btn-light-secondary btn-sm">
                                <i class="ti ti-arrow-left"></i> Back to All Brands
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Brand Name</label>
                                <input type="text" id="name" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $brand->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="is_active" class="form-label">Status</label>
                                <select id="is_active" name="is_active"
                                    class="form-select @error('is_active') is-invalid @enderror">
                                    <option value="1"
                                        {{ (string) old('is_active', (int) $brand->is_active) === '1' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="0"
                                        {{ (string) old('is_active', (int) $brand->is_active) === '0' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="logo" class="form-label">Brand Logo</label>
                                <input type="file" id="logo" name="logo"
                                    class="form-control @error('logo') is-invalid @enderror" accept="image/*">
                                <small class="text-muted">Any size is accepted and will be resized to 120x80
                                    automatically.</small>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($brand->logo_url)
                                    <div class="mt-2">
                                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->name }}"
                                            style="width: 120px; height: 80px; object-fit: contain; border-radius: 8px; background: #fff; border: 1px solid #edf0f7; padding: 4px;">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Brand</button>
                            <a href="{{ route('brands.index') }}" class="btn btn-light-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
