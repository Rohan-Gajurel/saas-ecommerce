@extends('layout.app')

@section('title')
    <title>Edit Product - SAAS Ecommerce Backend</title>
@endsection

@section('content')
<div class="row saas-section-anchor" id="products">
    <div class="col-12">
        <div class="card">

            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-0">Edit Product</h5>
                        <small class="text-muted">Update product information</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('products.index') }}" class="btn btn-light-secondary btn-sm">
                            <i class="ti ti-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card-body">

                @php
                    $selectedCategoryIds = collect(old('category_ids', $product->categories->pluck('id')->toArray()))
                        ->map(fn($id) => (int) $id)
                        ->all();

                    $topCategories = $categories->whereNull('parent_id')->sortBy('name')->values();
                    $subCategoriesByParent = $categories->whereNotNull('parent_id')->sortBy('name')->groupBy('parent_id');
                @endphp

                <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Product Name --}}
                    <div class="mb-3">
                        <label for="product_name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="product_name" name="name"
                            required value="{{ old('name', $product->name) }}">
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                    </div>

                    {{-- Price --}}
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" name="price"
                            step="0.01" required value="{{ old('price', $product->price) }}">
                    </div>

                    {{-- Stock --}}
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock"
                            required value="{{ old('stock', $product->stock) }}">
                    </div>


                    {{-- Existing Images --}}
                    @if($product->images->count())
                        <div class="mb-3">
                            <label class="form-label d-block">Current Images (Select Primary or Delete)</label>
                            <div class="d-flex flex-wrap gap-3">

                                @foreach($product->images as $image)
                                    <div class="border rounded p-2 text-center position-relative" style="width: 130px;">
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="Product image"
                                             class="img-fluid mb-2" style="height: 80px; object-fit: cover;">

                                        <div class="form-check d-flex justify-content-center mb-2">
                                            <input class="form-check-input" type="radio"
                                                name="primary_image_id" value="{{ $image->id }}"
                                                {{ $image->is_primary ? 'checked' : '' }}>
                                        </div>
                                        <small class="text-muted d-block">Primary</small>

                                        <button type="button" class="btn btn-sm btn-danger mt-2" 
                                            onclick="deleteImage({{ $image->id }})">
                                            <i class="ti ti-trash"></i> Delete
                                        </button>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                        <input type="hidden" id="images_to_delete" name="images_to_delete" value="">
                    @endif

                    {{-- Categories --}}
                    <div class="mb-3">
                        <label class="form-label d-block">Categories</label>

                        <div class="border rounded p-3">
                            @foreach($topCategories as $topCategory)
                                @php
                                    $subCats = $subCategoriesByParent->get($topCategory->id, collect());
                                @endphp

                                <div class="mb-3">

                                    {{-- Parent --}}
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input"
                                            id="category_{{ $topCategory->id }}"
                                            name="category_ids[]"
                                            value="{{ $topCategory->id }}"
                                            {{ in_array($topCategory->id, $selectedCategoryIds) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold"
                                            for="category_{{ $topCategory->id }}">{{ $topCategory->name }}</label>
                                    </div>

                                    {{-- Children --}}
                                    @if($subCats->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-3 ms-4">
                                            @foreach($subCats as $category)
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input"
                                                        id="category_{{ $category->id }}"
                                                        name="category_ids[]"
                                                        value="{{ $category->id }}"
                                                        data-parent-id="{{ $topCategory->id }}"
                                                        {{ in_array($category->id, $selectedCategoryIds) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="category_{{ $category->id }}">{{ $category->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                </div>

                            @endforeach
                        </div>

                        <small class="text-muted">You can select more than one category.</small>
                    </div>

                    {{-- Brand --}}
                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Brand</label>
                        <select class="form-control" id="brand_id" name="brand_id">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Primary Image Index for NEW uploads --}}
                    <input type="hidden" id="primary_image_index" name="primary_image_index"
                           value="{{ old('primary_image_index', $product->primary_image_index ?? 1) }}">

                    {{-- Upload New Images --}}
                    <div class="mb-3">
                        <label for="images" class="form-label">Product Images</label>
                        <input type="file" class="form-control" id="images" name="images[]" multiple>
                        <small class="text-muted">You can upload multiple images.</small>
                    </div>

                    {{-- New Image Preview --}}
                    <div class="mb-3" id="new-image-preview-section" style="display: none;">
                        <label class="form-label d-block">Selected Images (Choose Primary)</label>
                        <div id="new-image-preview" class="d-flex flex-wrap gap-3"></div>
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Product</button>
                        <button type="button" class="btn btn-light-secondary"
                            onclick="window.location='{{ route('products.index') }}'">Cancel</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ---------------------------------------------
       PARENT CATEGORY AUTO-CHECK
    ---------------------------------------------- */
    const categoryCheckboxes = document.querySelectorAll('input[name="category_ids[]"]');

    categoryCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', function () {
            const parentId = this.dataset.parentId;

            if (this.checked && parentId) {
                const parentBox = document.getElementById(`category_${parentId}`);
                if (parentBox) parentBox.checked = true;
            }
        });
    });

    // Auto check parents on page load
    categoryCheckboxes.forEach((checkbox) => {
        const parentId = checkbox.dataset.parentId;
        if (checkbox.checked && parentId) {
            const parentBox = document.getElementById(`category_${parentId}`);
            if (parentBox) parentBox.checked = true;
        }
    });


    /* ---------------------------------------------
       NEW IMAGE PREVIEW + PRIMARY SELECTOR
    ---------------------------------------------- */
    const imagesInput = document.getElementById('images');
    const previewSection = document.getElementById('new-image-preview-section');
    const previewContainer = document.getElementById('new-image-preview');
    const primaryIndexInput = document.getElementById('primary_image_index');

    if (!imagesInput) return;

    imagesInput.addEventListener('change', function () {
        previewContainer.innerHTML = '';

        const files = Array.from(this.files);

        if (files.length === 0) {
            previewSection.style.display = 'none';
            return;
        }

        previewSection.style.display = 'block';

        files.forEach((file, index) => {
            const label = document.createElement('label');
            label.className = 'border rounded p-2 text-center';
            label.style.width = '130px';
            label.style.cursor = 'pointer';

            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'img-fluid mb-2';
            img.style.height = '80px';
            img.style.objectFit = 'cover';

            const radioWrapper = document.createElement('div');
            radioWrapper.className = 'form-check d-flex justify-content-center';

            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.className = 'form-check-input';
            radio.name = 'primary_new_uploaded_image';
            radio.value = index + 1;

            if (index === 0) radio.checked = true;

            radio.addEventListener('change', function () {
                primaryIndexInput.value = this.value;
            });

            radioWrapper.appendChild(radio);

            const text = document.createElement('small');
            text.className = 'text-muted d-block';
            text.textContent = 'Primary';

            label.appendChild(img);
            label.appendChild(radioWrapper);
            label.appendChild(text);

            previewContainer.appendChild(label);
        });

        primaryIndexInput.value = 1;
    });

});

// Delete existing image
function deleteImage(imageId) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    const deleteInput = document.getElementById('images_to_delete');
    const currentIds = deleteInput.value ? deleteInput.value.split(',') : [];
    
    if (!currentIds.includes(String(imageId))) {
        currentIds.push(String(imageId));
    }
    
    deleteInput.value = currentIds.join(',');

    // Hide the image element
    event.target.closest('.border').remove();
}
</script>
@endsection