@extends('layout.app')
@section('title')
    <title>Create Product - SAAS Ecommerce Backend</title>
@endsection
@section('content')        
    <div class="row saas-section-anchor" id="products">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0">Create Product</h5>
                            <small class="text-muted">Add a new product to your system</small>
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
                        $selectedCategoryIds = collect(old('category_ids', []))->map(fn($id) => (int) $id)->all();
                        $topCategories = $categories
                            ->whereNull('parent_id')
                            ->sortBy('name')
                            ->values();

                        $subCategoriesByParent = $categories
                            ->whereNotNull('parent_id')
                            ->sortBy('name')
                            ->groupBy('parent_id');
                    @endphp
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- Product Name --}}
                        <div class="mb-3">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="name" placeholder="Enter product name" required value="{{ old('name') }}">
                        </div>
                        {{-- Description --}}
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        </div>
                        {{-- Price --}}
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" required value="{{ old('price') }}">
                        </div>
                        {{-- Stock --}}
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" required value="{{ old('stock') }}">
                        </div>
                        {{-- Categories --}}
                        <div class="mb-3">
                            <label class="form-label d-block">Categories</label>
                            <div class="border rounded p-3">
                                @foreach($topCategories as $topCategory)
                                    @php
                                        $subCategories = $subCategoriesByParent->get($topCategory->id, collect());
                                    @endphp

                                    <div class="mb-3">
                                        <div class="form-check mb-2">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="category_{{ $topCategory->id }}"
                                                name="category_ids[]"
                                                value="{{ $topCategory->id }}"
                                                {{ in_array($topCategory->id, $selectedCategoryIds) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label fw-bold" for="category_{{ $topCategory->id }}">
                                                {{ $topCategory->name }}
                                            </label>
                                        </div>

                                        @if($subCategories->isNotEmpty())
                                            <div class="d-flex flex-wrap gap-3 ms-4">
                                                @foreach($subCategories as $category)
                                                    <div class="form-check">
                                                        <input
                                                            class="form-check-input"
                                                            type="checkbox"
                                                            id="category_{{ $category->id }}"
                                                            name="category_ids[]"
                                                            value="{{ $category->id }}"
                                                            data-parent-id="{{ $topCategory->id }}"
                                                            {{ in_array($category->id, $selectedCategoryIds) ? 'checked' : '' }}
                                                        >
                                                        <label class="form-check-label" for="category_{{ $category->id }}">
                                                            {{ $category->name }}
                                                        </label>
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
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">Product Images</label>
                            <input type="file" class="form-control" id="images" name="images[]" multiple>
                            <small class="text-muted">You can upload multiple images.</small>
                        </div>

                        <input type="hidden" id="primary_image_index" name="primary_image_index" value="{{ old('primary_image_index', 1) }}">
                        <div class="mb-3" id="new-image-preview-section" style="display: none;">
                            <label class="form-label d-block">Selected Images (Choose Primary)</label>
                            <div id="new-image-preview" class="d-flex flex-wrap gap-3"></div>
                        </div>

                        {{-- Submit and Cancel Buttons --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create Product</button>
                            <button type="button" class="btn btn-light-secondary" onclick="window.location='{{ route('products.index') }}'">Cancel</button>
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
    const imagesInput = document.getElementById('images');
    const previewSection = document.getElementById('new-image-preview-section');
    const previewContainer = document.getElementById('new-image-preview');
    const primaryImageIndexInput = document.getElementById('primary_image_index');
    const categoryCheckboxes = document.querySelectorAll('input[name="category_ids[]"]');

    categoryCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', function () {
            if (!this.checked || !this.dataset.parentId) {
                return;
            }

            const parentCheckbox = document.getElementById(`category_${this.dataset.parentId}`);
            if (parentCheckbox) {
                parentCheckbox.checked = true;
            }
        });
    });

    categoryCheckboxes.forEach((checkbox) => {
        if (!checkbox.checked || !checkbox.dataset.parentId) {
            return;
        }

        const parentCheckbox = document.getElementById(`category_${checkbox.dataset.parentId}`);
        if (parentCheckbox) {
            parentCheckbox.checked = true;
        }
    });

    if (!imagesInput || !previewSection || !previewContainer || !primaryImageIndexInput) {
        return;
    }

    imagesInput.addEventListener('change', function () {
        previewContainer.innerHTML = '';

        const files = Array.from(imagesInput.files || []);
        if (!files.length) {
            previewSection.style.display = 'none';
            primaryImageIndexInput.value = '1';
            return;
        }

        previewSection.style.display = 'block';

        files.forEach((file, index) => {
            const imageIndex = index + 1;
            const wrapper = document.createElement('label');
            wrapper.className = 'border rounded p-2 text-center';
            wrapper.style.width = '130px';
            wrapper.style.cursor = 'pointer';

            const img = document.createElement('img');
            img.className = 'img-fluid mb-2';
            img.style.height = '80px';
            img.style.objectFit = 'cover';
            img.alt = file.name;
            img.src = URL.createObjectURL(file);

            const radioWrap = document.createElement('div');
            radioWrap.className = 'form-check d-flex justify-content-center';

            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.className = 'form-check-input';
            radio.name = 'new_primary_image_radio';
            radio.value = String(imageIndex);
            radio.checked = imageIndex === Number(primaryImageIndexInput.value || 1);
            radio.addEventListener('change', function () {
                primaryImageIndexInput.value = this.value;
            });

            const caption = document.createElement('small');
            caption.className = 'text-muted d-block';
            caption.textContent = 'Primary';

            radioWrap.appendChild(radio);
            wrapper.appendChild(img);
            wrapper.appendChild(radioWrap);
            wrapper.appendChild(caption);
            previewContainer.appendChild(wrapper);
        });

        const checked = previewContainer.querySelector('input[type="radio"]:checked');
        if (!checked) {
            const first = previewContainer.querySelector('input[type="radio"]');
            if (first) {
                first.checked = true;
                primaryImageIndexInput.value = first.value;
            }
        }
    });
});
</script>
@endsection