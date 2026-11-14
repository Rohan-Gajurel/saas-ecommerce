@extends('layout.app')

@section('title')
    <title>Product Details - SAAS Ecommerce Backend</title>
@endsection

@section('content')        
<div class="row saas-section-anchor" id="products">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">Product Details</h5>
                        <small class="text-muted">View detailed information about the product</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-light-secondary btn-sm">
                            <i class="ti ti-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>ID</th>
                        <td>{{ $product->id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $product->name }}</td>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <td>{{ $product->description }}</td>
                    </tr>
                    <tr>
                        <th>Price</th>
                        <td>{{ $product->price }}</td>
                    </tr>
                    <tr>
                        <th>Stock</th>
                        <td>{{ $product->stock }}</td>
                    </tr>
                    <tr>
                        <th>Categories</th>
                        <td>{{ $product->categories->pluck('name')->join(', ') ?: 'None' }}</td>
                    </tr>
                    <tr>
                        <th>Brand</th>
                        <td>{{ $product->brand->name ?? 'None' }}</td>
                    </tr>
                    <tr>
                        <th>Images</th>
                        <td>
                            @if($product->images->count())
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($product->images as $image)
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $image->path) }}" alt="Product image" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                            @if($image->is_primary)
                                                <div><small class="text-success">Primary</small></div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">No Images</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection