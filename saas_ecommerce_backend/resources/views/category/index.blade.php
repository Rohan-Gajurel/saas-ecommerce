@extends('layout.app')

@section('title')
    <title>Categories - SAAS Ecommerce Backend</title>
@endsection

@section('content')        
<div class="row saas-section-anchor" id="tenants">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">Categories</h5>
                    </div>

                    {{-- Alerts --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @elseif(session('delete'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('delete') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @elseif(session('update'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            {{ session('update') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
    
                    @can('category.create')
                    <div class="d-flex gap-2">
                        <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-plus"></i> Create Category
                        </a>
                    </div>
                    @endcan
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Slug</th>
                                <th>Image</th>
                                <th>Top Category</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>
                                    <strong>{{ $category->name }}</strong>
                                </td>
                                <td>{{ $category->slug }}</td>
                                <td >
                                    @if($category->image && file_exists(public_path('storage/' . $category->image)))
                                        <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $category->parent ? $category->parent->name : 'None' }}
                                </td>
                                <td class="text-end">
                                    @can('update', $category)
                                    <div class="btn-group">
                                        <a href="{{ route('categories.edit', $category->id) }}" 
                                           class="btn btn-sm btn-light-secondary">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                    @endcan

                                    @can('delete', $category)
                                        <form action="{{ route('categories.destroy', $category->id) }}" 
                                              method="POST" 
                                              class="delete-form d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.delete-form').forEach(form => {

        form.addEventListener('submit', function (event) {
            event.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

        });

    });

});
</script>
@endsection