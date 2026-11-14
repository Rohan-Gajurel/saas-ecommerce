@extends('layout.app')

@section('title')
    <title>Products - SAAS Ecommerce Backend</title>
@endsection

@section('content')        
<div class="row saas-section-anchor" id="products">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">Products</h5>
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

                    @can('products.create')
                    <div class="d-flex gap-2">
                        <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-plus"></i> Create Product
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
                                <th>Product</th>
                                <th>Image</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Category</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                </td>
                                <td>
                                    @if($product->primaryImage)
                                        <img src="{{ asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 70px; max-height: 70px;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $product->price }}
                                </td>
                                <td>
                                    {{ $product->stock }}
                                </td>
                                <td>
                                    {{ $product->categories->pluck('name')->join(', ') ?: 'None' }}
                                </td>
                                <td class="text-end">
                                    @can('viewProduct', $product)
                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info"><i class="ti ti-eye"></i></a>
                                    @endcan

                                    @can('updateProduct', $product)
                                    <div class="btn-group">
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-light-secondary"><i class="ti ti-edit"></i></a>
                                    </div>
                                    @endcan

                                    @can('deleteProduct', $product)
                                        <form action="{{ route('products.destroy', $product->id) }}" 
                                              method="POST" 
                                              class="delete-form d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
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